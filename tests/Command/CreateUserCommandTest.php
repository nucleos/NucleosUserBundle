<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Command;

use Nucleos\UserBundle\Command\CreateUserCommand;
use Nucleos\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateUserCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass', 'email', true, false));
        $exitCode      = $commandTester->execute([
            'username' => 'user',
            'email'    => 'email',
            'password' => 'pass',
        ], [
            'decorated'   => false,
            'interactive' => false,
        ]);

        self::assertSame(0, $exitCode, 'Returns 0 in case of success');
        self::assertMatchesRegularExpression('/Created user user/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper(): void
    {
        $application = new Application();

        $helper = $this->createMock(QuestionHelper::class);

        $helper->expects(self::exactly(3))
            ->method('ask')
            ->willReturn(
                'user',
                'email',
                'pass'
            )
        ;

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester(
            $this->getManipulator('user', 'pass', 'email', true, false),
            $application
        );
        $exitCode = $commandTester->execute([], [
            'decorated'   => false,
            'interactive' => true,
        ]);

        self::assertSame(0, $exitCode, 'Returns 0 in case of success');
        self::assertMatchesRegularExpression('/Created user user/', $commandTester->getDisplay());
    }

    private function createCommandTester(UserManipulator $manipulator, ?Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new CreateUserCommand($manipulator);

        $application->add($command);

        return new CommandTester($application->find('nucleos:user:create'));
    }

    /**
     * @return MockObject&UserManipulator
     */
    private function getManipulator(string $username, string $password, string $email, bool $active, bool $superadmin): MockObject
    {
        $manipulator = $this->createMock(UserManipulator::class);
        $manipulator
            ->expects(self::once())
            ->method('create')
            ->with($username, $password, $email, $active, $superadmin)
        ;

        return $manipulator;
    }
}
