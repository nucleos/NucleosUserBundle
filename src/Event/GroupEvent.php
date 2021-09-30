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

use Nucleos\UserBundle\Model\GroupInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class GroupEvent extends Event
{
    private GroupInterface $group;

    private Request $request;

    public function __construct(GroupInterface $group, Request $request)
    {
        $this->group   = $group;
        $this->request = $request;
    }

    public function getGroup(): GroupInterface
    {
        return $this->group;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
