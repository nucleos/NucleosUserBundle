<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Symfony\Bundle\SecurityBundle\Security;

trait DoctrineSetupTrait
{
    /**
     * @param string[] $roles
     */
    public static function createUser(
        ?string $username = null,
        array $roles = []
    ): TestUser {
        $entity = new TestUser();
        $entity->setPlainPassword('password');

        $username ??= ('my-user'.$entity->getId());

        $entity->setUsername($username);
        $entity->setEmail(\sprintf('%s@localhost', $username));
        $entity->setRoles($roles);

        return $entity;
    }

    protected function persist(object ...$objects): void
    {
        $manager = $this->getEntityManager();

        foreach ($objects as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }

    protected function getSecurity(): Security
    {
        $manager = self::getContainer()->get(Security::class);

        \assert($manager instanceof Security);

        return $manager;
    }

    private function getUser(int $id): ?TestUser
    {
        return $this->getEntityManager()->find(TestUser::class, $id);
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        \assert($manager instanceof EntityManagerInterface);

        return $manager;
    }
}
