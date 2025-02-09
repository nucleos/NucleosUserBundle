<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Tests\App\Entity\TestGroup;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', ['secret' => 'secret']);

    $containerConfigurator->extension('framework', ['test' => true]);

    $containerConfigurator->extension('framework', ['session' => ['storage_factory_id' => 'session.storage.factory.mock_file', 'handler_id' => null]]);

    $containerConfigurator->extension('framework', [
        'mailer' => [
            'dsn'      => 'null://null',
        ],
    ]);

    $containerConfigurator->extension('twig', ['strict_variables' => true]);

    $containerConfigurator->extension('twig', ['exception_controller' => null]);

    $securityConfig = [
        'firewalls'  => ['main' => [
            'security'   => true,
            'form_login' => [
                'login_path'          => 'nucleos_user_security_login',
                'check_path'          => 'nucleos_user_security_check',
                'default_target_path' => '/profile',
            ],
        ]],
    ];

    // TODO: Remove if when dropping support of Symfony 5.4
    if (!class_exists(IsGranted::class)) {
        $securityConfig['enable_authenticator_manager'] = true;
    }

    $containerConfigurator->extension('security', $securityConfig);

    $containerConfigurator->extension('security', [
        'providers' => ['nucleos_userbundle' => ['id' => 'nucleos_user.user_provider.username']],
    ]);

    $containerConfigurator->extension('security', ['access_control' => [['path' => '^/.*', 'role' => 'PUBLIC_ACCESS']]]);

    $containerConfigurator->extension('security', ['password_hashers' => [UserInterface::class => [
        'algorithm'        => 'plaintext',
    ]]]);

    $containerConfigurator->extension('doctrine', ['dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/data.db', 'logging' => false, 'use_savepoints' => true]]);

    $containerConfigurator->extension('doctrine', ['orm' => [
        'auto_mapping' => true,
        'mappings'     => [
            'App' => [
                'is_bundle' => false,
                'type'      => 'attribute',
                'dir'       => '%kernel.project_dir%/Entity',
                'prefix'    => 'Nucleos\UserBundle\Tests\App\Entity',
                'alias'     => 'App',
            ],
        ],
    ]]);

    $containerConfigurator->extension('nucleos_user', ['db_driver' => 'orm']);

    $containerConfigurator->extension('nucleos_user', ['firewall_name' => 'main']);

    $containerConfigurator->extension('nucleos_user', ['from_email' => 'no-reply@localhost']);

    $containerConfigurator->extension('nucleos_user', ['user_class' => TestUser::class]);

    $containerConfigurator->extension('nucleos_user', ['group' => ['group_class' => TestGroup::class]]);

    $containerConfigurator->extension('nucleos_user', ['loggedin' => ['route' => 'nucleos_user_update_security']]);

    $containerConfigurator->extension('nucleos_user', ['deletion' => [
    ]]);

    $containerConfigurator->extension('dama_doctrine_test', ['enable_static_connection' => true, 'enable_static_meta_data_cache' => true, 'enable_static_query_cache' => true]);
};
