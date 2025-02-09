<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongoClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserListener implements EventSubscriber
{
    private readonly UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher     = $userPasswordHasher;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
            $this->recomputeChangeSet($args->getObjectManager(), $object);
        }
    }

    private function updateUserFields(UserInterface $user): void
    {
        if (null === $user->getPlainPassword()) {
            return;
        }

        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $user->getPlainPassword())
        );
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata(\get_class($user));

        if ($om instanceof EntityManager && $meta instanceof ORMClassMetadata) {
            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);

            return;
        }

        if ($om instanceof DocumentManager && $meta instanceof MongoClassMetadata) {
            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($meta, $user);
        }
    }
}
