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

use Nucleos\UserBundle\Util\UserManipulator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class CreateUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'nucleos:user:create';

    private UserManipulator $userManipulator;

    public function __construct(UserManipulator $userManipulator)
    {
        parent::__construct();

        $this->userManipulator = $userManipulator;
    }

    protected function configure(): void
    {
        $this
            ->setName('nucleos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>nucleos:user:create</info> command creates a user:

  <info>php %command.full_name% matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php %command.full_name% matthieu matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php %command.full_name% admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php %command.full_name% thibault --inactive</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $this->userManipulator->create($username, $password, $email, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $questions['username'] = $this->createUsernameQuestion();
        }

        if (!$input->getArgument('email')) {
            $questions['email'] = $this->createEmailQuestion();
        }

        if (!$input->getArgument('password')) {
            $questions['password'] = $this->createPasswordQuestion();
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

    private function createPasswordQuestion(): Question
    {
        $question = new Question('Please choose a password:');
        $question->setValidator(
            static function ($password) {
                if ('' === trim($password)) {
                    throw new RuntimeException('Password can not be empty');
                }

                return $password;
            }
        );
        $question->setHidden(true);

        return $question;
    }

    private function createEmailQuestion(): Question
    {
        $question = new Question('Please choose an email:');
        $question->setValidator(
            static function ($email) {
                if ('' === trim($email)) {
                    throw new RuntimeException('Email can not be empty');
                }

                return $email;
            }
        );

        return $question;
    }
}
