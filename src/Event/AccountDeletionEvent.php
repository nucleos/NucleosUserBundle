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

namespace Nucleos\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AccountDeletionEvent extends Event
{
    private Request $request;

    private UserInterface $user;

    public function __construct(UserInterface $user, Request $request)
    {
        $this->user      = $user;
        $this->request   = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
