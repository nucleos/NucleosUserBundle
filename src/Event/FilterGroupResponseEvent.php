<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Event;

use Nucleos\UserBundle\Model\GroupInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FilterGroupResponseEvent extends GroupEvent
{
    private ?Response $response;

    public function __construct(GroupInterface $group, Request $request, Response $response)
    {
        parent::__construct($group, $request);

        $this->response = $response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
