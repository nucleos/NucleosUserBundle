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

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Event\GetResponseLoginEvent;
use Nucleos\UserBundle\Form\Type\LoginFormType;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class LoginAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->twig             = $twig;
        $this->eventDispatcher  = $eventDispatcher;
        $this->formFactory      = $formFactory;
        $this->router           = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __invoke(Request $request): Response
    {
        $event = new GetResponseLoginEvent($request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::SECURITY_LOGIN_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->create(LoginFormType::class, null, [
            'action' => $this->router->generate('nucleos_user_security_check'),
            'method' => 'POST',
        ]);

        $error = null;
        if ($form->getErrors()->count() > 0) {
            $error = $form->getErrors()->current()->getMessage();
        }

        return new Response($this->twig->render('@NucleosUser/Security/login.html.twig', [
            'form'          => $form->createView(),
            // TODO: Remove this fields with the next major release
            'last_username' => $form->getData()['_username'],
            'error'         => $error,
            'csrf_token'    => $this->csrfTokenManager->getToken('authenticate'),
        ]));
    }
}
