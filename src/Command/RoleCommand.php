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

namespace Nucleos\UserBundle\Command;

use InvalidArgumentException;
use Nucleos\UserBundle\Util\UserManipulator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class RoleCommand extends Command
{
    private UserManipulator $userManipulator;

    public function __construct(UserManipulator $userManipulator)
    {
        parent::__construct();

        $this->userManipulator = $userManipulator;
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $role     = $input->getArgument('role');
        $super    = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new RuntimeException('Not enough arguments.');
        }

        $manipulator = $this->userManipulator;
        $this->executeRoleCommand($manipulator, $output, $username, $super, $role);

        return 0;
    }

    /**
     * @see Command
     */
    abstract protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, string $username, bool $super, string $role): void;

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $questions['username'] = $this->createUsernameQuestion();
        }

        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $questions['role'] = $this->createRoleQuestion();
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    private function createUsernameQuestion(): Question
    {
        $question = new Question('Please choose a username:');
        $question->setValidator(
            static function ($username) {
                if ('' === trim($username)) {
                    throw new RuntimeException('Username can not be empty');
                }

                return $username;
            }
        );

        return $question;
    }

    private function createRoleQuestion(): Question
    {
        $question = new Question('Please choose a role:');
        $question->setValidator(
            static function ($role) {
                if ('' === trim($role)) {
                    throw new RuntimeException('Role can not be empty');
                }

                return $role;
            }
        );

        return $question;
    }
}
