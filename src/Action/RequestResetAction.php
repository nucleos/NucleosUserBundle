<?php

/*
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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    /**
     * @var UserProviderInterface<UserInterface>
     */
    private readonly UserProviderInterface $userProvider;

    private readonly TranslatorInterface $translator;

    /**
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     *
     * @param UserProviderInterface<UserInterface> $userProvider
     */
    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        UserManager $userManager,
        TokenGenerator $tokenGenerator,
        UserProviderInterface $userProvider,
        ResettingMailer $mailer,
        int $retryTtl,
        TranslatorInterface $translator
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
        $this->translator      = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $this->process($request);

            $this->getFlashBag($request)
                ?->add('success', $this->translator->trans('resetting.check_email', [
                    '%tokenLifetime%' => ceil($this->retryTtl / 3600),
                ], 'NucleosUserBundle'))
            ;

            if (null !== $response) {
                return $response;
            }

            return new RedirectResponse($this->router->generate('nucleos_user_resetting_request'));
        }

        return new Response($this->twig->render('@NucleosUser/Resetting/request.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
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
            return null;
        }

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::RESETTING_SEND_EMAIL_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if ($user->isPasswordRequestNonExpired($this->retryTtl)) {
            return null;
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

        return null;
    }

    private function getFlashBag(Request $request): ?FlashBagInterface
    {
        $session = $request->hasSession() ? $request->getSession() : null;

        if (!$session instanceof Session) {
            return null;
        }

        return $session->getFlashBag();
    }

    private function createForm(): FormInterface
    {
        return $this->formFactory
            ->create(RequestPasswordFormType::class, null, [
                'action' => $this->router->generate('nucleos_user_resetting_request'),
                'method' => 'POST',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'resetting.request.submit',
            ])
        ;
    }
}
