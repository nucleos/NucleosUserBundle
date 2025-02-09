<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use Nucleos\UserBundle\Event\FormEvent;
use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Form\Model\Resetting;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResettingListener implements EventSubscriberInterface
{
    private readonly UrlGeneratorInterface $router;

    private readonly int $tokenTtl;

    public function __construct(UrlGeneratorInterface $router, int $tokenTtl)
    {
        $this->router   = $router;
        $this->tokenTtl = $tokenTtl;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::RESETTING_RESET_INITIALIZE => 'onResettingResetInitialize',
            NucleosUserEvents::RESETTING_RESET_SUCCESS    => 'onResettingResetSuccess',
            NucleosUserEvents::RESETTING_RESET_REQUEST    => 'onResettingResetRequest',
        ];
    }

    public function onResettingResetInitialize(GetResponseUserEvent $event): void
    {
        if (!$event->getUser()->isPasswordRequestNonExpired($this->tokenTtl)) {
            $event->setResponse(new RedirectResponse($this->router->generate('nucleos_user_resetting_request')));
        }
    }

    public function onResettingResetSuccess(FormEvent $event): void
    {
        $model = $event->getForm()->getData();

        if (!$model instanceof Resetting) {
            return;
        }

        $user = $model->getUser();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
    }

    public function onResettingResetRequest(GetResponseUserEvent $event): void
    {
        if (!$event->getUser()->isAccountNonLocked()) {
            $event->setResponse(new RedirectResponse($this->router->generate('nucleos_user_resetting_request')));
        }
    }
}
