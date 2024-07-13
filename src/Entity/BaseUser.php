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

namespace Nucleos\UserBundle\Entity;

use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Model\User;

/**
 * @phpstan-template GroupTemplate of GroupInterface
 *
 * @phpstan-extends  User<GroupTemplate>
 */
class BaseUser extends User {}
