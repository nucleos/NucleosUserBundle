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

namespace Nucleos\UserBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RouteCollection;

final class RoutingTest extends TestCase
{
    /**
     * @dataProvider loadRoutingProvider
     */
    public function testLoadRouting(string $routeName, string $path, array $methods): void
    {
        $locator = new FileLocator();
        $loader  = new XmlFileLoader($locator);

        $collection = new RouteCollection();
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/change_password.xml'));

        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/resetting.xml');
        $subCollection->addPrefix('/resetting');
        $collection->addCollection($subCollection);
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/security.xml'));

        $route = $collection->get($routeName);
        static::assertNotNull($route, sprintf('The route "%s" should exists', $routeName));
        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
    }

    public function loadRoutingProvider(): array
    {
        return [
            ['nucleos_user_change_password', '/change-password', ['GET', 'POST']],

            ['nucleos_user_resetting_request', '/resetting/request', ['GET']],
            ['nucleos_user_resetting_send_email', '/resetting/send-email', ['POST']],
            ['nucleos_user_resetting_check_email', '/resetting/check-email', ['GET']],
            ['nucleos_user_resetting_reset', '/resetting/reset/{token}', ['GET', 'POST']],

            ['nucleos_user_security_login', '/login', ['GET', 'POST']],
            ['nucleos_user_security_check', '/login_check', ['POST']],
            ['nucleos_user_security_logout', '/logout', ['GET', 'POST']],
        ];
    }
}
