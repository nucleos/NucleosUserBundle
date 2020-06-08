<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

use DateTime;

abstract class TrustedDevice implements TrustedDeviceInterface
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string|null
     */
    protected $confirmationCode;

    /**
     * @var int
     */
    protected $confirmationRetry = 0;

    /**
     * @var DateTime|null
     */
    protected $confirmationValidUntil;

    /**
     * @var string|null
     */
    protected $lastIp;

    /**
     * @var DateTime|null
     */
    protected $lastSeen;

    public function __construct(UserInterface $user, string $token)
    {
        $this->user  = $user;
        $this->token = $token;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getLastIp(): ?string
    {
        return $this->lastIp;
    }

    public function getLastSeen(): ?DateTime
    {
        return $this->lastSeen;
    }

    public function updateLastSeen(DateTime $date, string $ip): void
    {
        $this->lastSeen = $date;
        $this->lastIp   = $ip;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    public function getConfirmationRetry(): int
    {
        return $this->confirmationRetry;
    }

    public function getConfirmationValidUntil(): ?DateTime
    {
        return $this->confirmationValidUntil;
    }
}
