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

use Nucleos\UserBundle\Event\FilterUserResponseEvent;
use Nucleos\UserBundle\Event\FormEvent;
use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Form\Model\TwoFactorToken;
use Nucleos\UserBundle\Form\Type\TwoFactorFormType;
use Nucleos\UserBundle\Model\TrustedDeviceManagerInterface;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class TwoFactorLoginAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TrustedDeviceManagerInterface
     */
    private $trustedDeviceManager;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        Security $security,
        FormFactoryInterface $formFactory,
        TrustedDeviceManagerInterface $trustedDeviceManager
    ) {
        $this->twig                     = $twig;
        $this->router                   = $router;
        $this->eventDispatcher          = $eventDispatcher;
        $this->security                 = $security;
        $this->formFactory              = $formFactory;
        $this->trustedDeviceManager     = $trustedDeviceManager;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            return new RedirectResponse($this->router->generate('nucleos_user_security_login'));
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_LOGIN_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->create(TwoFactorFormType::class, new TwoFactorToken($user), [
            'validation_groups' => ['ResetPassword', 'Default'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_LOGIN_SUCCESS);

            // TODO: Save Token

            if (null === $response = $event->getResponse()) {
                $url      = $this->router->generate('nucleos_user_security_loggedin');
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(
                new FilterUserResponseEvent($user, $request, $response),
                NucleosUserEvents::TWO_FACTOR_LOGIN_COMPLETED
            );

            return $response;
        }

        return new Response($this->twig->render('@NucleosUser/TwoFactor/login.html.twig', [
            'form'  => $form->createView(),
        ]));
    }

    private function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }
}
