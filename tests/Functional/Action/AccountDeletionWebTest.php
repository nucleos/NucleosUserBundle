<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Functional\Action;

use Nucleos\UserBundle\Action\AccountDeletionAction;
use Nucleos\UserBundle\Tests\Functional\DoctrineSetupTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(AccountDeletionAction::class)]
final class AccountDeletionWebTest extends WebTestCase
{
    use DoctrineSetupTrait;

    #[Test]
    public function execute(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->loginUser($user);
        $client->request('GET', '/delete');

        self::assertResponseIsSuccessful();

        $client->submitForm('account_deletion_form[delete]', [
            'account_deletion_form[current_password]'  => $user->getPlainPassword(),
            'account_deletion_form[confirm]'           => true,
        ]);

        self::assertResponseRedirects('/logout');
        $client->followRedirect();

        self::assertNull($this->getSecurity()->getUser());
        self::assertNull($this->getUser($user->getId()));
    }

    #[Test]
    public function executeWithInvalidPassword(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->loginUser($user);
        $client->request('GET', '/delete');

        self::assertResponseIsSuccessful();

        $client->submitForm('account_deletion_form[delete]', [
            'account_deletion_form[current_password]'  => 'wrong',
            'account_deletion_form[confirm]'           => true,
        ]);

        self::assertResponseIsSuccessful();

        self::assertSelectorTextContains('#account_deletion_form', 'The entered password is invalid');

        self::assertNotNull($this->getUser($user->getId()));
    }
}
