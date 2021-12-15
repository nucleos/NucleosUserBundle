<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
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
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\PasswordUpdaterInterface;

final class UserListener implements EventSubscriber
{
    private PasswordUpdaterInterface $passwordUpdater;

    private CanonicalFieldsUpdater $canonicalFieldsUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->passwordUpdater        = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
            $this->recomputeChangeSet($args->getObjectManager(), $object);
        }
    }

    private function updateUserFields(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
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
