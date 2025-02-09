<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Nucleos\UserBundle\DependencyInjection\Compiler\InjectUserCheckerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NucleosUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new InjectUserCheckerPass());

        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $mappings = [
            (string) realpath(__DIR__.'/Resources/config/doctrine-mapping') => 'Nucleos\UserBundle\Model',
        ];

        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['nucleos_user.model_manager_name'], 'nucleos_user.backend_type_orm'));
        }
        if (class_exists(DoctrineMongoDBMappingsPass::class)) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, ['nucleos_user.model_manager_name'], 'nucleos_user.backend_type_mongodb'));
        }
    }
}
