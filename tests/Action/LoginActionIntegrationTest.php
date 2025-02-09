<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Action;

use Nucleos\UserBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \Nucleos\UserBundle\Action\LoginAction
 */
final class LoginActionIntegrationTest extends WebTestCase
{
    private readonly KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLogin(): void
    {
        $this->client->followRedirects(true);
        $this->client->request('GET', '/login');

        self::assertResponseStatusCodeSame(200);
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
