<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\EventListener;

use Nucleos\UserBundle\Event\UserEvent;
use Nucleos\UserBundle\EventListener\LocaleEventListener;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\LocaleAwareInterface as LocaleAwareTranslator;

final class LocaleEventListenerTest extends TestCase
{
    /**
     * @var LocaleAwareTranslator&MockObject
     */
    private LocaleAwareTranslator $translator;

    private LocaleEventListener $listener;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(LocaleAwareTranslator::class);
        $this->listener   = new LocaleEventListener($this->translator);
    }

    #[Test]
    public function getSubscribedEvents(): void
    {
        self::assertSame([
            NucleosUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN          => 'onSecurityInteractiveLogin',
            KernelEvents::REQUEST                      => [['onKernelRequest', 20]],
            NucleosUserEvents::USER_LOCALE_CHANGED     => 'onLocaleChanged',
            NucleosUserEvents::USER_TIMEZONE_CHANGED   => 'onTimezoneChanged',
        ], LocaleEventListener::getSubscribedEvents());
    }

    #[Test]
    public function onImplicitLogin(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $user = new TestUser();
        $user->setLocale('fr');
        $user->setTimezone('Europe/Paris');

        $event = new UserEvent($user, $request);

        $this->translator->expects(self::once())
            ->method('setLocale')
            ->with('fr')
        ;

        $this->listener->onImplicitLogin($event);

        self::assertSame('fr', $request->getLocale());
        self::assertSame('fr', $session->get('_locale'));
        self::assertSame('Europe/Paris', $session->get('_timezone'));
    }

    #[Test]
    public function onSecurityInteractiveLogin(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $user = new TestUser();
        $user->setLocale('fr');
        $user->setTimezone('Europe/Paris');

        $event = new InteractiveLoginEvent($request, new TestBrowserToken(user: $user));

        $this->translator->expects(self::once())
            ->method('setLocale')
            ->with('fr')
        ;

        $this->listener->onSecurityInteractiveLogin($event);

        self::assertSame('fr', $request->getLocale());
        self::assertSame('fr', $session->get('_locale'));
        self::assertSame('Europe/Paris', $session->get('_timezone'));
    }

    #[Test]
    public function onKernelRequest(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('_locale', 'fr');
        $session->set('_timezone', 'Europe/Paris');

        $request = new Request();
        $request->setSession($session);
        $request->cookies->set($session->getName(), $session->getId());

        $event = new RequestEvent($this->createMock(KernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->translator->expects(self::once())
            ->method('setLocale')
            ->with('fr')
        ;

        $this->listener->onKernelRequest($event);

        self::assertSame('fr', $request->getLocale());
        self::assertSame('fr', $session->get('_locale'));
        self::assertSame('Europe/Paris', $session->get('_timezone'));
    }

    #[Test]
    public function onTimezoneChanged(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $user = new TestUser();
        $user->setTimezone('Europe/Paris');

        $event = new UserEvent($user, $request);

        $this->listener->onTimezoneChanged($event);

        self::assertSame('Europe/Paris', $session->get('_timezone'));
    }

    #[Test]
    public function onLocaleChanged(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $user = new TestUser();
        $user->setLocale('de');

        $event = new UserEvent($user, $request);

        $this->translator->expects(self::once())
            ->method('setLocale')
            ->with('de')
        ;

        $this->listener->onLocaleChanged($event);

        self::assertSame('de', $request->getLocale());
        self::assertSame('de', $session->get('_locale'));
    }
}
