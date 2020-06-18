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

namespace Nucleos\UserBundle\Mailer;

use Nucleos\UserBundle\Mailer\Mail\ResettingMail;
use Nucleos\UserBundle\Mailer\Mail\TwoFactorMail;
use Nucleos\UserBundle\Model\TrustedDeviceInterface;
use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Mailer implements MailerInterface, TwoFactorMailer
{
    /**
     * @var SymfonyMailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var string
     */
    private $fromEmail;

    public function __construct(SymfonyMailer $mailer, TranslatorInterface $translator, UrlGeneratorInterface $router, string $fromEmail)
    {
        $this->mailer     = $mailer;
        $this->translator = $translator;
        $this->router     = $router;
        $this->fromEmail  = $fromEmail;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $url  = $this->router->generate('nucleos_user_resetting_reset', [
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = (new ResettingMail())
            ->from(Address::fromString($this->fromEmail))
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('resetting.email.subject', [
                '%username%' => $user->getUsername(),
            ], 'NucleosUserBundle'))
            ->setUser($user)
            ->setConfirmationUrl($url)
        ;

        $this->mailer->send($mail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendTwoFactorMessage(UserInterface $user, TrustedDeviceInterface $token): void
    {
        $url  = $this->router->generate('nucleos_user_two_factor_login', [
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = (new TwoFactorMail())
            ->from(Address::fromString($this->fromEmail))
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('two_factor.email.subject', [
                '%username%' => $user->getUsername(),
            ], 'NucleosUserBundle'))
            ->setUser($user)
            ->setConfirmationUrl($url)
            ->setToken($token)
        ;

        $this->mailer->send($mail);
    }
}
