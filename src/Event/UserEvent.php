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

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    protected ?Request $request = null;

    protected UserInterface $user;

    public function __construct(UserInterface $user, Request $request = null)
    {
        $this->user    = $user;
        $this->request = $request;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
