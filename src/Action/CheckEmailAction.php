<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @deprecated
 */
final class CheckEmailAction
{
    private readonly Environment $twig;

    private readonly RouterInterface $router;

    private readonly int $retryTtl;

    public function __construct(Environment $twig, RouterInterface $router, int $retryTtl)
    {
        $this->twig     = $twig;
        $this->router   = $router;
        $this->retryTtl = $retryTtl;
    }

    public function __invoke(Request $request): Response
    {
        $username = $request->query->get('username', '');

        \assert(\is_string($username));

        if ('' === trim($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->router->generate('nucleos_user_resetting_request'));
        }

        return new Response($this->twig->render('@NucleosUser/Resetting/check_email.html.twig', [
            'tokenLifetime' => ceil($this->retryTtl / 3600),
        ]));
    }
}
