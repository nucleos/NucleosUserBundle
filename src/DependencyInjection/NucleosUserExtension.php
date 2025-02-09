<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Nucleos\UserBundle\Mailer\ResettingMailer;
use Nucleos\UserBundle\Model\GroupManager;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\Util\TokenGenerator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class NucleosUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array<array{registry: string, tag: string, events: string[]}>
     */
    private static array $doctrineDrivers = [
        'orm'     => [
            'registry' => 'doctrine',
            'tag'      => 'doctrine.event_listener',
            'events'   => [
                Events::prePersist,
                Events::preUpdate,
            ],
        ],
        'mongodb' => [
            'registry' => 'doctrine_mongodb',
            'tag'      => 'doctrine_mongodb.odm.event_listener',
            'events'   => [
                Events::prePersist,
                Events::preUpdate,
            ],
        ],
    ];

    private bool $mailerNeeded  = false;

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor     = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ('custom' !== $config['db_driver']) {
            if (isset(self::$doctrineDrivers[$config['db_driver']])) {
                $loader->load('doctrine.php');
                $container->setAlias('nucleos_user.doctrine_registry', new Alias(self::$doctrineDrivers[$config['db_driver']]['registry'], false));
            } else {
                $loader->load(\sprintf('%s.php', $config['db_driver']));
            }
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db_driver'], true);
        }

        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $definition = $container->getDefinition('nucleos_user.object_manager');
            $definition->setFactory([new Reference('nucleos_user.doctrine_registry'), 'getManager']);
        }

        foreach (['validator', 'security', 'util', 'mailer', 'listeners', 'commands'] as $basename) {
            $loader->load(\sprintf('%s.php', $basename));
        }

        if (!$config['use_authentication_listener']) {
            $container->removeDefinition('nucleos_user.listener.authentication');
        }

        if ($config['use_flash_notifications']) {
            $loader->load('flash_notifications.php');
        }

        $container->setAlias('nucleos_user.util.token_generator', new Alias($config['service']['token_generator'], true));
        $container->setAlias(TokenGenerator::class, new Alias($config['service']['token_generator'], true));

        $container->setAlias('nucleos_user.user_manager', new Alias($config['service']['user_manager'], true));
        $container->setAlias(UserManager::class, new Alias($config['service']['user_manager'], true));

        if ($config['use_listener'] && isset(self::$doctrineDrivers[$config['db_driver']])) {
            $listenerDefinition = $container->getDefinition('nucleos_user.user_listener');
            foreach (self::$doctrineDrivers[$config['db_driver']]['events'] as $event) {
                $listenerDefinition->addTag(self::$doctrineDrivers[$config['db_driver']]['tag'], ['event' => $event]);
            }
        }

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'db_driver'          => 'nucleos_user.storage',
                'firewall_name'      => 'nucleos_user.firewall_name',
                'model_manager_name' => 'nucleos_user.model_manager_name',
                'user_class'         => 'nucleos_user.model.user.class',
            ],
        ]);

        $this->loadUpdateSecurity($loader);
        $this->loadDeletion($config['deletion'], $loader);
        $this->loadResetting($config['resetting'], $container, $loader, $config['from_email']);
        $this->loadLoggedin($config['loggedin'], $container);

        if (isset($config['group'])) {
            $this->loadGroups($config['group'], $container, $loader, $config['db_driver']);
        }

        if ($this->mailerNeeded) {
            $container->setAlias('nucleos_user.mailer', new Alias($config['service']['mailer'], true));
            $container->setAlias(ResettingMailer::class, new Alias($config['service']['mailer'], true));
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('nucleos_user');

        $storage = null;
        foreach ($configs as $config) {
            if (isset($config['db_driver'])) {
                $storage = $config['db_driver'];
            }
        }

        if (null === $storage || !isset(self::$doctrineDrivers[$storage])) {
            return;
        }

        $container->prependExtensionConfig('framework', [
            'validation' => [
                'mapping' => [
                    'paths' => [
                        __DIR__.'/../Resources/config/storage-validation/'.$storage,
                    ],
                ],
            ],
        ]);
    }

    private function remapParameters(array $config, ContainerBuilder $container, array $map): void
    {
        foreach ($map as $name => $paramName) {
            if (\array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     */
    private function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces): void
    {
        foreach ($namespaces as $ns => $map) {
            if ('' !== $ns) {
                if (!\array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }

            if (\is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(\sprintf($map, $name), $value);
                }
            }
        }
    }

    private function loadUpdateSecurity(FileLoader $loader): void
    {
        $loader->load('update_security.php');
    }

    private function loadDeletion(array $config, FileLoader $loader): void
    {
        if (true !== $config['enabled']) {
            return;
        }

        $loader->load('deletion.php');
    }

    private function loadResetting(array $config, ContainerBuilder $container, FileLoader $loader, string $fromEmail): void
    {
        $this->mailerNeeded = true;
        $loader->load('resetting.php');

        if (isset($config['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['from_email'];
            unset($config['from_email']);
        }

        $container->setParameter('nucleos_user.resetting.from_email', $fromEmail);

        $this->remapParametersNamespaces($config, $container, [
            ''      => [
                'retry_ttl' => 'nucleos_user.resetting.retry_ttl',
                'token_ttl' => 'nucleos_user.resetting.token_ttl',
            ],
            'email' => 'nucleos_user.resetting.email.%s',
        ]);
    }

    private function loadGroups(array $config, ContainerBuilder $container, FileLoader $loader, string $dbDriver): void
    {
        if ('custom' !== $dbDriver) {
            if (isset(self::$doctrineDrivers[$dbDriver])) {
                $loader->load('doctrine_group.php');
            } else {
                $loader->load(\sprintf('%s_group.php', $dbDriver));
            }
        }

        $container->setAlias('nucleos_user.group_manager', new Alias($config['group_manager'], true));
        $container->setAlias(GroupManager::class, new Alias('nucleos_user.group_manager', true));

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'group_class' => 'nucleos_user.model.group.class',
            ],
        ]);
    }

    private function loadLoggedin(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('nucleos_user.loggedin.route', $config['route']);
    }
}
