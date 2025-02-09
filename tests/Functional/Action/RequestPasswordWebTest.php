<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Functional\Action;

use Nucleos\UserBundle\Action\RequestResetAction;
use Nucleos\UserBundle\Action\ResetAction;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Nucleos\UserBundle\Tests\Functional\DoctrineSetupTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(RequestResetAction::class)]
#[CoversClass(ResetAction::class)]
class RequestPasswordWebTest extends WebTestCase
{
    use DoctrineSetupTrait;

    #[Test]
    public function execute(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $this->performRequest($client, $user);

        $user = $this->getUser($user->getId());

        \assert(null !== $user);

        $this->performConfirmation($client, $user);
    }

    #[Test]
    public function executeLegacyRoute(): void
    {
        $client = self::createClient();

        $this->persist(
            $user  = self::createUser(),
        );

        $client->request('GET', '/resetting/');

        self::assertResponseRedirects('/resetting/request');
    }

    private function performRequest(KernelBrowser $client, TestUser $user): void
    {
        $client->request('GET', '/resetting/request');

        self::assertResponseIsSuccessful();

        $client->submitForm('save', [
            'username' => $user->getUsername(),
        ]);

        self::assertResponseRedirects('/resetting/request');
        $client->followRedirect();

        self::assertSelectorTextContains('.flash-success', 'An email has been sent');
    }

    private function performConfirmation(KernelBrowser $client, TestUser $user): void
    {
        $oldPassword = $user->getPassword();

        $client->request('GET', \sprintf('/resetting/reset/%s', $user->getConfirmationToken()));

        self::assertResponseIsSuccessful();

        $client->submitForm('resetting_form[save]', [
            'resetting_form[plainPassword][first]'  => 'new-secret-password',
            'resetting_form[plainPassword][second]' => 'new-secret-password',
        ]);

        self::assertResponseRedirects('/update-security');

        self::assertNotNull($this->getSecurity()->getUser());
        self::assertNotSame($oldPassword, $this->getUser($user->getId())?->getPassword());
    }
}
