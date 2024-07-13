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

namespace Nucleos\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nucleos_user');

        $rootNode = $treeBuilder->getRootNode();

        $this->addMainSection($rootNode);
        $this->addResettingSection($rootNode);
        $this->addDeletionSection($rootNode);
        $this->addGroupSection($rootNode);
        $this->addServiceSection($rootNode);
        $this->addLoggedinSection($rootNode);

        return $treeBuilder;
    }

    private function addMainSection(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->booleanNode('use_authentication_listener')->defaultTrue()->end()
                ->booleanNode('use_flash_notifications')->defaultTrue()->end()
                ->scalarNode('from_email')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    private function addResettingSection(NodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('retry_ttl')->defaultValue(7200)->end()
                        ->scalarNode('token_ttl')->defaultValue(86400)->end()
                        ->scalarNode('from_email')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addDeletionSection(NodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('deletion')
                    ->canBeEnabled()
                ->end()
            ->end()
        ;
    }

    private function addServiceSection(NodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('mailer')->defaultValue('nucleos_user.mailer.simple')->end()
                            ->scalarNode('token_generator')->defaultValue('nucleos_user.util.token_generator.simple')->end()
                            ->scalarNode('user_manager')->defaultValue('nucleos_user.user_manager.default')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addGroupSection(NodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('group')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('group_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('group_manager')->defaultValue('nucleos_user.group_manager.default')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addLoggedinSection(NodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('loggedin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
