<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Functional\Action;

use Nucleos\UserBundle\Action\UpdateSecurityAction;
use Nucleos\UserBundle\Tests\Functional\DoctrineSetupTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(UpdateSecurityAction::class)]
final class UpdateSecurityWebTest extends WebTestCase
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
        $client->request('GET', '/update-security');

        self::assertResponseIsSuccessful();

        $oldPassword = $user->getPassword();

        $client->submitForm('update_security_form[save]', [
            'update_security_form[current_password]'      => $user->getPlainPassword(),
            'update_security_form[plainPassword][first]'  => 'new-secret-password',
            'update_security_form[plainPassword][second]' => 'new-secret-password',
        ]);

        self::assertResponseRedirects('/update-security');

        self::assertNotSame($oldPassword, $this->getUser($user->getId())?->getPassword());
    }

    #[Test]
    public function executeLegacyRoute(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->loginUser($user);
        $client->request('GET', '/change-password');

        self::assertResponseRedirects('/update-security');
    }
}
