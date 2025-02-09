<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Nucleos\UserBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

if (!($loader = @include __DIR__.'/../vendor/autoload.php')) {
    echo <<<'EOT'
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT;

    exit(1);
}

function bootstrap(): void
{
    $kernels = [
        AppKernel::class,
    ];

    foreach ($kernels as $kernel) {
        $kernel = new $kernel('test', false);
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->run(new ArrayInput([
            'command' => 'doctrine:database:create',
            '--quiet' => '1',
        ]));

        $application->run(new ArrayInput([
            'command' => 'doctrine:schema:create',
            '--quiet' => '1',
        ]));

        $kernel->shutdown();
    }
}

bootstrap();
