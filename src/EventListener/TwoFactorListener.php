<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Model\TrustedDeviceManagerInterface;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

final class TwoFactorListener implements EventSubscriberInterface
{
    public const TWO_FACTOR_EXISTS = '2FA_exists';

    /**
     * @var TrustedDeviceManagerInterface
     */
    private $trustedDeviceManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $cookieName;

    public function __construct(
        TrustedDeviceManagerInterface $trustedDeviceManager,
        Request $request,
        Session $session,
        Router $router,
        string $cookieName
    ) {
        $this->trustedDeviceManager = $trustedDeviceManager;
        $this->request              = $request;
        $this->session              = $session;
        $this->cookieName           = $cookieName;
        $this->router               = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'authSuccess',
            NucleosUserEvents::SECURITY_LOGIN_COMPLETED  => 'loginCompleted',
        ];
    }

    /**
     * Method is called every time a user is authenticated.
     */
    public function authSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface && $this->isValidDeviceToken($user)) {
            $this->session->set(self::TWO_FACTOR_EXISTS, true);

            // TODO: Update Device Token "lastSeen"
        }
    }

    /**
     * Method is called after using the login form.
     */
    public function loginCompleted(GetResponseUserEvent $event): void
    {
        if ($this->session->has(self::TWO_FACTOR_EXISTS)) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->router->generate('nucleos_user_two_factor_send')));
    }

    private function isValidDeviceToken(UserInterface $user): bool
    {
        if ($this->request->cookies->has($this->cookieName)) {
            return false;
        }

        $token = $this->request->cookies->get($this->cookieName);

        if (null === $token) {
            return false;
        }

        return null !== $this->trustedDeviceManager->findToken($user, $token);
    }
}
