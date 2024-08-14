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

namespace Nucleos\UserBundle\Tests\DependencyInjection;

use Nucleos\UserBundle\Action\AccountDeletionAction;
use Nucleos\UserBundle\DependencyInjection\NucleosUserExtension;
use Nucleos\UserBundle\EventListener\FlashListener;
use Nucleos\UserBundle\Form\Type\AccountDeletionFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class NucleosUserExtensionTest extends TestCase
{
    protected ContainerBuilder $configuration;

    public function testUserLoadThrowsExceptionUnlessFirewallNameSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new NucleosUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['firewall_name']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadThrowsExceptionUnlessGroupModelClassSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new NucleosUserExtension();
        $config = $this->getFullConfig();
        unset($config['group']['group_class']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadThrowsExceptionUnlessUserModelClassSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new NucleosUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['user_class']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadModelClassWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Acme\MyBundle\Document\User', 'nucleos_user.model.user.class');
    }

    public function testUserLoadModelClass(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Entity\User', 'nucleos_user.model.user.class');
    }

    public function testUserLoadManagerClass(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter('custom', 'nucleos_user.model_manager_name');
        $this->assertAlias('acme_my.user_manager', 'nucleos_user.user_manager');
        $this->assertAlias('nucleos_user.group_manager.default', 'nucleos_user.group_manager');
    }

    public function testUserLoadUtilServiceWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('custom_loggedin', 'nucleos_user.loggedin.route');
        $this->assertAlias('nucleos_user.mailer.simple', 'nucleos_user.mailer');
    }

    public function testUserLoadUtilService(): void
    {
        $this->createFullConfiguration();

        $this->assertAlias('acme_my.mailer', 'nucleos_user.mailer');
    }

    public function testUserLoadFlashesByDefault(): void
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition(FlashListener::class);
    }

    public function testUserLoadFlashesCanBeDisabled(): void
    {
        $this->createFullConfiguration();

        $this->assertNotHasDefinition(FlashListener::class);
    }

    public function testUserLoadDeletionSrviceWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertNotHasDefinition(AccountDeletionAction::class);
        $this->assertNotHasDefinition(AccountDeletionFormType::class);
    }

    public function testUserLoadDeletionSrvice(): void
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition(AccountDeletionAction::class);
        $this->assertHasDefinition(AccountDeletionFormType::class);
    }

    protected function createEmptyConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader              = new NucleosUserExtension();
        $config              = $this->getEmptyConfig();
        $loader->load([$config], $this->configuration);
        self::assertInstanceOf(ContainerBuilder::class, $this->configuration);
    }

    protected function createFullConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader              = new NucleosUserExtension();
        $config              = $this->getFullConfig();
        $loader->load([$config], $this->configuration);
        self::assertInstanceOf(ContainerBuilder::class, $this->configuration);
    }

    protected function getEmptyConfig(): array
    {
        $yaml = <<<'EOF'
firewall_name: nucleos_user
user_class: Acme\MyBundle\Document\User
from_email: Acme Corp <admin@acme.org>
loggedin:
    route: custom_loggedin
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig(): array
    {
        $yaml = <<<'EOF'
firewall_name: nucleos_user
use_flash_notifications: false
user_class: Acme\MyBundle\Entity\User
model_manager_name: custom
from_email: Acme Corp <admin@acme.org>
resetting:
    retry_ttl: 7200
    token_ttl: 86400
    from_email: Acme Corp <reset@acme.org>
service:
    mailer: acme_my.mailer
    user_manager: acme_my.user_manager
group:
    group_class: Acme\MyBundle\Entity\Group
deletion:
    enabled: true
loggedin:
    route: custom_loggedin
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function assertAlias(string $value, string $key): void
    {
        self::assertSame($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed $value
     */
    private function assertParameter($value, string $key): void
    {
        self::assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition(string $id): void
    {
        self::assertTrue($this->configuration->hasDefinition($id) ? true : $this->configuration->hasAlias($id));
    }

    private function assertNotHasDefinition(string $id): void
    {
        self::assertFalse($this->configuration->hasDefinition($id) ? true : $this->configuration->hasAlias($id));
    }
}
