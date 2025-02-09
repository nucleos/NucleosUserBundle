<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\DependencyInjection;

use Nucleos\UserBundle\DependencyInjection\Configuration;
use Nucleos\UserBundle\Model\Group;
use Nucleos\UserBundle\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testOptions(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [$this->basicConfig()]);

        $expected = [
            'firewall_name'               => 'main',
            'from_email'                  => 'no-reply@localhost',
            'user_class'                  => User::class,
            'group'                       => [
                'group_class'   => Group::class,
                'group_manager' => 'nucleos_user.group_manager.default',
            ],
            'loggedin'                    => [
                'route' => 'custom_loggedin',
            ],
            'db_driver'                   => 'noop',
            'model_manager_name'          => null,
            'use_authentication_listener' => true,
            'use_listener'                => true,
            'use_flash_notifications'     => true,
            'resetting'                   => [
                'retry_ttl' => 7200,
                'token_ttl' => 86400,
            ],
            'deletion'                    => [
                'enabled' => false,
            ],
            'service'                     => [
                'mailer'                 => 'nucleos_user.mailer.simple',
                'token_generator'        => 'nucleos_user.util.token_generator.simple',
                'user_manager'           => 'nucleos_user.user_manager.default',
            ],
        ];

        self::assertSame($expected, $config);
    }

    /**
     * @return array<string, mixed>
     */
    private function basicConfig(): array
    {
        return [
            'firewall_name' => 'main',
            'from_email'    => 'no-reply@localhost',
            'user_class'    => User::class,
            'group'         => [
                'group_class' => Group::class,
            ],
            'loggedin'      => [
                'route' => 'custom_loggedin',
            ],
        ];
    }
}
