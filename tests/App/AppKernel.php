<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\App;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nucleos\UserBundle\NucleosUserBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();

        yield new TwigBundle();

        yield new SecurityBundle();

        yield new DoctrineBundle();

        yield new DAMADoctrineTestBundle();

        yield new NucleosUserBundle();
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@NucleosUserBundle/Resources/config/routing/security.php');
        $routes->import('@NucleosUserBundle/Resources/config/routing/update_security.php');
        $routes->import('@NucleosUserBundle/Resources/config/routing/resetting.php')
            ->prefix('/resetting')
        ;
        $routes->import('@NucleosUserBundle/Resources/config/routing/deletion.php');
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__.'/config/config.php');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/app-bundle/var/';
    }
}
