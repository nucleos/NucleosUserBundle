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
use Symfony\Component\Console\Output\OutputInterface;

final class DemoteUserCommand extends RoleCommand
{
    protected static $defaultName = 'nucleos:user:demote';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('nucleos:user:demote')
            ->setDescription('Demote a user by removing a role')
            ->setHelp(
                <<<'EOT'
The <info>nucleos:user:demote</info> command demotes a user by removing a role

  <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
  <info>php %command.full_name% --super matthieu</info>
EOT
            )
        ;
    }

    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, string $username, bool $super, string $role): void
    {
        if ($super) {
            $manipulator->demote($username);
            $output->writeln(sprintf('User "%s" has been demoted as a simple user. This change will not apply until the user logs out and back in again.', $username));
        } elseif ($manipulator->removeRole($username, $role)) {
            $output->writeln(sprintf('Role "%s" has been removed from user "%s". This change will not apply until the user logs out and back in again.', $role, $username));
        } else {
            $output->writeln(sprintf('User "%s" didn\'t have "%s" role.', $username, $role));
        }
    }
}
