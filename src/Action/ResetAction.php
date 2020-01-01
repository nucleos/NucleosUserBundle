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
use Nucleos\UserBundle\Form\Model\Resetting;
use Nucleos\UserBundle\Form\Type\ResettingFormType;
use Nucleos\UserBundle\Model\UserManagerInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ResetAction
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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * ResetAction constructor.
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        UserManagerInterface $userManager
    ) {
        $this->twig            = $twig;
        $this->router          = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory     = $formFactory;
        $this->userManager     = $userManager;
    }

    public function __invoke(Request $request, string $token): Response
    {
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->router->generate('nucleos_user_security_login'));
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_RESET_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->create(ResettingFormType::class, new Resetting(), [
            'validation_groups' => ['ResetPassword', 'Default'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_RESET_SUCCESS);

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url      = $this->router->generate('nucleos_user_security_loggedin');
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(
                new FilterUserResponseEvent($user, $request, $response),
                NucleosUserEvents::RESETTING_RESET_COMPLETED
            );

            return $response;
        }

        return new Response($this->twig->render('@NucleosUser/Resetting/reset.html.twig', [
            'token' => $token,
            'form'  => $form->createView(),
        ]));
    }
}
