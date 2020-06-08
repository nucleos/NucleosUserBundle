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
use Nucleos\UserBundle\Mailer\TwoFactorMailer;
use Nucleos\UserBundle\Model\TrustedDeviceInterface;
use Nucleos\UserBundle\Model\TrustedDeviceManagerInterface;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SendTwoFactorTokenAction
{
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
     * @var TrustedDeviceManagerInterface
     */
    private $trustedDeviceManager;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var TwoFactorMailer
     */
    private $mailer;

    /**
     * @var int
     */
    private $tokenLength;

    /**
     * @var int
     */
    private $tokenTtl;

    /**
     * @var int
     */
    private $retryTtl;

    /**
     * SendTwoFactorTokenAction constructor.
     */
    public function __construct(
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        Security $security,
        TrustedDeviceManagerInterface $trustedDeviceManager,
        TokenGeneratorInterface $tokenGenerator,
        TwoFactorMailer $mailer,
        int $tokenLength,
        int $tokenTtl,
        int $retryTtl
    ) {
        $this->router                  = $router;
        $this->eventDispatcher         = $eventDispatcher;
        $this->security                = $security;
        $this->trustedDeviceManager    = $trustedDeviceManager;
        $this->tokenGenerator          = $tokenGenerator;
        $this->mailer                  = $mailer;
        $this->tokenLength             = $tokenLength;
        $this->tokenTtl                = $tokenTtl;
        $this->retryTtl                = $retryTtl;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            return new RedirectResponse($this->router->generate('nucleos_user_security_login'));
        }

        $response = $this->process($request, $user);

        if (null !== $response) {
            return $response;
        }

        return new RedirectResponse($this->router->generate('nucleos_user_two_factor_login'));
    }

    private function process(Request $request, UserInterface $user): ?Response
    {
        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_SEND_EMAIL_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (!$this->hasActiveToken($user)) {
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_LOGIN_REQUEST);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $token = $this->createToken($user);

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_SEND_EMAIL_CONFIRM);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            // TODO: Send 2FA token mail
            $this->mailer->sendTwoFactorMessage($user, $token);

            $this->userManager->updateUser($user);

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::TWO_FACTOR_SEND_EMAIL_COMPLETED);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return null;
    }

    private function createToken(UserInterface $user): ?TrustedDeviceInterface
    {
        $token    = substr($this->tokenGenerator->generateToken(), 0, $this->tokenLength);

        return $this->trustedDeviceManager->createToken(
            $user,
            $token,
            new DateTime('+'.$this->tokenTtl.' seconds')
        );
    }

    private function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }

    private function hasActiveToken(UserInterface $user): bool
    {
        // TODO: Check for active token

        return false;
    }
}
