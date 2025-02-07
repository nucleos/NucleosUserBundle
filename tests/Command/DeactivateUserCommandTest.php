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

namespace Nucleos\UserBundle\Tests\Command;

use Nucleos\UserBundle\Command\DeactivateUserCommand;
use Nucleos\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

final class DeactivateUserCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user'));
        $exitCode      = $commandTester->execute([
            'username' => 'user',
        ], [
            'decorated'   => false,
            'interactive' => false,
        ]);

        self::assertSame(0, $exitCode, 'Returns 0 in case of success');
        self::assertMatchesRegularExpression('/User "user" has been deactivated/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper(): void
    {
        $application = new Application();

        $helper = $this->createMock(QuestionHelper::class);

        $helper->expects(self::once())
            ->method('ask')
            ->willReturn('user')
        ;

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getManipulator('user'), $application);
        $exitCode      = $commandTester->execute([], [
            'decorated'   => false,
            'interactive' => true,
        ]);

        self::assertSame(0, $exitCode, 'Returns 0 in case of success');
        self::assertMatchesRegularExpression('/User "user" has been deactivated/', $commandTester->getDisplay());
    }

    private function createCommandTester(UserManipulator $manipulator, ?Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new DeactivateUserCommand($manipulator);

        $application->add($command);

        return new CommandTester($application->find('nucleos:user:deactivate'));
    }

    /**
     * @return MockObject&UserManipulator
     */
    private function getManipulator(string $username): MockObject
    {
        $manipulator = $this->createMock(UserManipulator::class);
        $manipulator
            ->expects(self::once())
            ->method('deactivate')
            ->with($username)
        ;

        return $manipulator;
    }
}
