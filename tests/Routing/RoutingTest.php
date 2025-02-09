<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Routing;

use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\RouteCollection;

final class RoutingTest extends TestCase
{
    /**
     * @dataProvider provideLoadRoutingCases
     *
     * @param string[] $methods
     */
    public function testLoadRouting(string $routeName, string $path, array $methods): void
    {
        $locator = new FileLocator();
        $loader  = new PhpFileLoader($locator);

        $collection = new RouteCollection();
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/update_security.php'));

        $subCollection = $loader->load(__DIR__.'/../../src/Resources/config/routing/resetting.php');
        $subCollection->addPrefix('/resetting');
        $collection->addCollection($subCollection);
        $collection->addCollection($loader->load(__DIR__.'/../../src/Resources/config/routing/security.php'));

        $route = $collection->get($routeName);
        self::assertNotNull($route, \sprintf('The route "%s" should exists', $routeName));
        self::assertSame($path, $route->getPath());
        self::assertSame($methods, $route->getMethods());
    }

    /**
     * @phpstan-return Generator<array{string, string, string[]}>
     */
    public static function provideLoadRoutingCases(): iterable
    {
        yield ['nucleos_user_update_security', '/update-security', ['GET', 'POST']];

        yield ['nucleos_user_update_security_legacy', '/change-password', ['GET', 'POST']];

        yield ['nucleos_user_resetting_request', '/resetting/request', ['GET', 'POST']];

        yield ['nucleos_user_resetting_check_email', '/resetting/check-email', ['GET']];

        yield ['nucleos_user_resetting_reset', '/resetting/reset/{token}', ['GET', 'POST']];

        yield ['nucleos_user_security_login', '/login', ['GET', 'POST']];

        yield ['nucleos_user_security_check', '/login_check', ['POST']];

        yield ['nucleos_user_security_logout', '/logout', ['GET', 'POST']];
    }
}
