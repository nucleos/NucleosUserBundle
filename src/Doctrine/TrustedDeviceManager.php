<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Doctrine;

use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nucleos\UserBundle\Model\TrustedDeviceInterface;
use Nucleos\UserBundle\Model\TrustedDeviceManager as BaseTrustedDeviceManager;
use Nucleos\UserBundle\Model\UserInterface;

final class TrustedDeviceManager extends BaseTrustedDeviceManager
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var string
     */
    private $class;

    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->objectManager = $om;
        $this->repository    = $om->getRepository($class);

        $metadata    = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    public function findToken(UserInterface $user, string $token): ?TrustedDeviceInterface
    {
        $trustedDevice = $this->repository->findOneBy([
            'user'  => $user,
            'token' => $token,
        ]);

        if ($trustedDevice instanceof TrustedDeviceInterface) {
            return $trustedDevice;
        }

        return null;
    }

    public function removeExpired(): void
    {
        $this->repository->createQueryBuilder('t')
            ->delete()
            ->where('t.confirmationValidUntil < :now')->setParameter('now', new DateTime());
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
