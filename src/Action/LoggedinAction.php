<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class LoggedinAction
{
    /**
     * @var Environment
     */
    private $twig;

    public function __invoke(Request $request): Response
    {
        return new Response($this->twig->render('@NucleosUser/Security/loggedin.html.twig'));
    }
}
