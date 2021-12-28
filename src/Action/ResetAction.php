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
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ResetAction
{
    private Environment $twig;

    private RouterInterface $router;

    private EventDispatcherInterface $eventDispatcher;

    private FormFactoryInterface $formFactory;

    private UserManager $userManager;

    private string $loggedinRoute;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        UserManager $userManager,
        string $loggedinRoute
    ) {
        $this->twig            = $twig;
        $this->router          = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory     = $formFactory;
        $this->userManager     = $userManager;
        $this->loggedinRoute   = $loggedinRoute;
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

        $form = $this->formFactory
            ->create(ResettingFormType::class, $formModel = new Resetting($user), [
                'validation_groups' => ['ResetPassword', 'Default'],
            ])
            ->add('save', SubmitType::class, [
                'label'  => 'resetting.reset.submit',
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_RESET_SUCCESS);

            $user->setPlainPassword($formModel->getPlainPassword());

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $response = new RedirectResponse($this->router->generate($this->loggedinRoute));
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
