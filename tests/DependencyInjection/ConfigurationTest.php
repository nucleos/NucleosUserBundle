<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\DependencyInjection;

use Nucleos\UserBundle\DependencyInjection\Configuration;
use Nucleos\UserBundle\Tests\App\Entity\TestGroup;
use Nucleos\UserBundle\Tests\App\Entity\TestToken;
use Nucleos\UserBundle\Tests\App\Entity\TestTrustedDevice;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultOptions(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [$this->basicConfig()]);

        $expected = [
            'firewall_name'               => 'test',
            'from_email'                  => 'no-reply@localhost',
            'user_class'                  => TestUser::class,
            'db_driver'                   => 'noop',
            'model_manager_name'          => null,
            'use_authentication_listener' => true,
            'use_listener'                => true,
            'use_flash_notifications'     => true,
            'resetting'                   => [
                'retry_ttl' => 7200,
                'token_ttl' => 86400,
            ],
            'service' => [
                'mailer'                 => 'nucleos_user.mailer.default',
                'email_canonicalizer'    => 'nucleos_user.util.canonicalizer.default',
                'token_generator'        => 'nucleos_user.util.token_generator.default',
                'username_canonicalizer' => 'nucleos_user.util.canonicalizer.default',
                'user_manager'           => 'nucleos_user.user_manager.default',
            ],
        ];

        static::assertSame($expected, $config);
    }

    public function testOptionsWithGroup(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [$this->basicConfigWithGroup()]);

        $expected = [
            'firewall_name'               => 'test',
            'from_email'                  => 'no-reply@localhost',
            'user_class'                  => TestUser::class,
            'group'                       => [
                'group_class'   => TestGroup::class,
                'group_manager' => 'nucleos_user.group_manager.default',
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
            'service' => [
                'mailer'                 => 'nucleos_user.mailer.default',
                'email_canonicalizer'    => 'nucleos_user.util.canonicalizer.default',
                'token_generator'        => 'nucleos_user.util.token_generator.default',
                'username_canonicalizer' => 'nucleos_user.util.canonicalizer.default',
                'user_manager'           => 'nucleos_user.user_manager.default',
            ],
        ];

        static::assertSame($expected, $config);
    }

    public function testOptionsWithTwoFactor(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [$this->basicConfigWithTwoFactor()]);

        $expected = [
            'firewall_name'                      => 'test',
            'from_email'                         => 'no-reply@localhost',
            'user_class'                         => TestUser::class,
            'two_factor'                         => [
                'token_class'          => TestToken::class,
                'trusted_device_class' => TestTrustedDevice::class,
                'token_length'         => 5,
                'token_ttl'            => 1800,
                'retry_delay'          => 300,
                'retry_limit'          => 5,
                'cookie_name'          => 'device_token',
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
            'service' => [
                'mailer'                 => 'nucleos_user.mailer.default',
                'email_canonicalizer'    => 'nucleos_user.util.canonicalizer.default',
                'token_generator'        => 'nucleos_user.util.token_generator.default',
                'username_canonicalizer' => 'nucleos_user.util.canonicalizer.default',
                'user_manager'           => 'nucleos_user.user_manager.default',
            ],
        ];

        static::assertSame($expected, $config);
    }

    /**
     * @return array<string, mixed>
     */
    private function basicConfig(): array
    {
        return [
            'firewall_name' => 'test',
            'from_email'    => 'no-reply@localhost',
            'user_class'    => TestUser::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function basicConfigWithGroup(): array
    {
        return [
            'firewall_name' => 'test',
            'from_email'    => 'no-reply@localhost',
            'user_class'    => TestUser::class,
            'group'         => [
                'group_class' => TestGroup::class,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function basicConfigWithTwoFactor(): array
    {
        return [
            'firewall_name'        => 'test',
            'from_email'           => 'no-reply@localhost',
            'user_class'           => TestUser::class,
            'two_factor'           => [
                'token_class'          => TestToken::class,
                'trusted_device_class' => TestTrustedDevice::class,
            ],
        ];
    }
}
