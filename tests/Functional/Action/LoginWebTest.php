<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Functional\Action;

use Nucleos\UserBundle\Action\CheckLoginAction;
use Nucleos\UserBundle\Action\LoginAction;
use Nucleos\UserBundle\Action\LogoutAction;
use Nucleos\UserBundle\Tests\Functional\DoctrineSetupTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(LoginAction::class)]
#[CoversClass(CheckLoginAction::class)]
#[CoversClass(LogoutAction::class)]
final class LoginWebTest extends WebTestCase
{
    use DoctrineSetupTrait;

    #[Test]
    public function execute(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $client->submitForm('save', [
            '_username'  => $user->getUsername(),
            '_password'  => $user->getPlainPassword(),
        ]);

        self::assertResponseRedirects('/profile');

        $client->request('GET', '/logout');
    }

    #[Test]
    public function executeWithInvalidPassword(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->request('GET', '/login');
        $client->followRedirects();

        self::assertResponseIsSuccessful();

        $client->submitForm('save', [
            '_username'  => $user->getUsername(),
            '_password'  => 'wrong',
        ]);

        self::assertSelectorTextContains('li', 'Invalid credentials.');
    }
}
