<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Mailer;

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SimpleResettingMailer implements ResettingMailer
{
    private readonly SymfonyMailer $mailer;

    private readonly TranslatorInterface $translator;

    private readonly UrlGeneratorInterface $router;

    private readonly string $fromEmail;

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

        $mail = (new TemplatedEmail())
            ->textTemplate('@NucleosUser/Resetting/email.txt.twig')
            ->htmlTemplate('@NucleosUser/Resetting/email.html.twig')
            ->from(Address::create($this->fromEmail))
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('resetting.email.subject', [
                '%username%' => $user->getUsername(),
            ], 'NucleosUserBundle'))
            ->context([
                'user'             => $user,
                'confirmationUrl'  => $url,
            ])
        ;

        $this->mailer->send($mail);
    }
}
