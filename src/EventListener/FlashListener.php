<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use InvalidArgumentException;
use Nucleos\UserBundle\NucleosUserEvents;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static array $successMessages = [
        NucleosUserEvents::UPDATE_SECURITY_COMPLETED => 'update_security.flash.success',
        NucleosUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
        NucleosUserEvents::ACCOUNT_DELETION_SUCCESS  => 'deletion.success',
    ];

    private readonly RequestStack $requestStack;

    private readonly TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack   = $requestStack;
        $this->translator     = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::UPDATE_SECURITY_COMPLETED => 'addSuccessFlash',
            NucleosUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
            NucleosUserEvents::ACCOUNT_DELETION_SUCCESS  => 'addSuccessFlash',
        ];
    }

    public function addSuccessFlash(Event $event, string $eventName): void
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    private function trans(string $message, array $params = []): string
    {
        return $this->translator->trans($message, $params, 'NucleosUserBundle');
    }

    private function getFlashBag(): FlashBagInterface
    {
        $session = $this->requestStack->getSession();

        if (!$session instanceof Session) {
            throw new RuntimeException('Could not retrieve flashbag from session.');
        }

        return $session->getFlashBag();
    }
}
