<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Mailer;

use Nucleos\UserBundle\Mailer\SimpleResettingMailer;
use Nucleos\UserBundle\Model\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SimpleMailerTest extends TestCase
{
    /**
     * @var MockObject&SymfonyMailer
     */
    private $swiftMailer;

    /**
     * @var MockObject&TranslatorInterface
     */
    private $translator;

    /**
     * @var MockObject&UrlGeneratorInterface
     */
    private $generator;

    protected function setUp(): void
    {
        $this->swiftMailer = $this->createMock(SymfonyMailer::class);
        $this->translator  = $this->createMock(TranslatorInterface::class);
        $this->generator   = $this->createMock(UrlGeneratorInterface::class);
    }

    public function testSendResettingEmail(): void
    {
        $mailer = $this->getMailer();

        $this->translator->method('trans')->with(self::anything(), self::anything(), self::anything())
            ->willReturnArgument(0)
        ;

        $this->generator->method('generate')->with(self::anything(), self::anything(), self::anything())
            ->willReturn('http://something.local')
        ;

        $this->swiftMailer->expects(self::once())->method('send')->with(self::isInstanceOf(TemplatedEmail::class));

        $mailer->sendResettingEmailMessage($this->getUser());
    }

    private function getMailer(): SimpleResettingMailer
    {
        return new SimpleResettingMailer(
            $this->swiftMailer,
            $this->translator,
            $this->generator,
            'foo@example.com'
        );
    }

    /**
     * @return MockObject&UserInterface
     */
    private function getUser(): MockObject
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->method('getEmail')
            ->willReturn('foo@bar.baz')
        ;

        return $user;
    }
}
