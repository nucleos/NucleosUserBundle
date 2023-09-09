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

use DateTime;
use Nucleos\UserBundle\Event\GetResponseNullableUserEvent;
use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Form\Type\RequestPasswordFormType;
use Nucleos\UserBundle\Mailer\ResettingMailer;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Util\TokenGenerator;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class RequestResetAction
{
    private readonly Environment $twig;

    private readonly FormFactoryInterface $formFactory;

    private readonly RouterInterface $router;

    private readonly EventDispatcherInterface $eventDispatcher;

    private readonly UserManager $userManager;

    private readonly TokenGenerator $tokenGenerator;

    private readonly ResettingMailer $mailer;

    private readonly int $retryTtl;

    private readonly UserProviderInterface $userProvider;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        UserManager $userManager,
        TokenGenerator $tokenGenerator,
        UserProviderInterface $userProvider,
        ResettingMailer $mailer,
        int $retryTtl
    ) {
        $this->twig            = $twig;
        $this->formFactory     = $formFactory;
        $this->router          = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager     = $userManager;
        $this->tokenGenerator  = $tokenGenerator;
        $this->userProvider    = $userProvider;
        $this->mailer          = $mailer;
        $this->retryTtl        = $retryTtl;
    }

    public function __invoke(Request $request): Response
    {
        $response = $this->process($request);

        if (null !== $response) {
            return $response;
        }

        $form = $this->formFactory
            ->create(RequestPasswordFormType::class, null, [
                'action' => $this->router->generate('nucleos_user_resetting_request'),
                'method' => 'POST',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'resetting.request.submit',
            ])
        ;

        return new Response($this->twig->render('@NucleosUser/Resetting/request.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function process(Request $request): ?Response
    {
        $username = (string) $request->request->get('username', '');

        if ('' === trim($username)) {
            return null;
        }

        $user = null;

        try {
            $user = '' === $username ? null : $this->userProvider->loadUserByIdentifier($username);
        } catch (UserNotFoundException) {
        }

        if (!$user instanceof UserInterface) {
            return new RedirectResponse($this->router->generate('nucleos_user_resetting_check_email'));
        }

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_SEND_EMAIL_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if ($user->isPasswordRequestNonExpired($this->retryTtl)) {
            return new RedirectResponse($this->router->generate('nucleos_user_resetting_check_email'));
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_RESET_REQUEST);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_SEND_EMAIL_CONFIRM);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $this->mailer->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new DateTime());
        $this->userManager->updateUser($user);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_SEND_EMAIL_COMPLETED);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        return new RedirectResponse($this->router->generate('nucleos_user_resetting_check_email'));
    }
}
