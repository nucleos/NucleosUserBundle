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

use Nucleos\UserBundle\Event\AccountDeletionEvent;
use Nucleos\UserBundle\Event\AccountDeletionResponseEvent;
use Nucleos\UserBundle\Event\GetResponseAccountDeletionEvent;
use Nucleos\UserBundle\Form\Model\AccountDeletion;
use Nucleos\UserBundle\Form\Type\AccountDeletionFormType;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManagerInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class AccountDeletionAction
{
    private Environment $twig;

    private RouterInterface $router;

    private UserManagerInterface $userManager;

    private TokenStorageInterface $tokenStorage;

    private FormFactoryInterface $formFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserManagerInterface $userManager,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->twig            = $twig;
        $this->router          = $router;
        $this->userManager     = $userManager;
        $this->formFactory     = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage    = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('Access Denied.');
        }

        $event = new GetResponseAccountDeletionEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::ACCOUNT_DELETION_INITIALIZE);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        $form = $this->formFactory
            ->create(AccountDeletionFormType::class, new AccountDeletion(), [
                'action'  => $this->router->generate('nucleos_user_delete_account'),
            ])
            ->add('delete', SubmitType::class, [
                'label'     => 'deletion.submit',
            ])
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processDeletion($user, $request);
        }

        return new Response($this->twig->render('@NucleosUser/Account/deletion.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    private function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return null;
        }

        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }

    private function processDeletion(UserInterface $user, Request $request): Response
    {
        $event = new AccountDeletionEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::ACCOUNT_DELETION);

        $this->userManager->deleteUser($user);

        $event = new AccountDeletionResponseEvent($user, $request, new RedirectResponse($this->router->generate('nucleos_user_security_logout')));
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::ACCOUNT_DELETION_SUCCESS);

        return $event->getResponse();
    }
}
