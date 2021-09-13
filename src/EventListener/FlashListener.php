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

namespace Nucleos\UserBundle\EventListener;

use InvalidArgumentException;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static array $successMessages = [
        NucleosUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        NucleosUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
        NucleosUserEvents::ACCOUNT_DELETION_SUCCESS  => 'deletion.success',
    ];

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $this->flashBag   = $flashBag;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            NucleosUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
            NucleosUserEvents::ACCOUNT_DELETION_SUCCESS  => 'addSuccessFlash',
        ];
    }

    public function addSuccessFlash(Event $event, string $eventName): void
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->flashBag->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    private function trans(string $message, array $params = []): string
    {
        return $this->translator->trans($message, $params, 'NucleosUserBundle');
    }
}
