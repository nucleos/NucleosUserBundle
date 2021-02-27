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

namespace Nucleos\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Pattern extends Constraint
{
    /**
     * @var string
     */
    public $minUpperMessage = 'nucleos_user.pattern.requires_upper';

    /**
     * @var string
     */
    public $minLowerMessage = 'nucleos_user.pattern.requires_lower';

    /**
     * @var string
     */
    public $minNumericMessage = 'nucleos_user.pattern.requires_numeric';

    /**
     * @var string
     */
    public $minSpecialMessage = 'nucleos_user.pattern.requires_special';

    /**
     * @var int
     */
    public $minUpper = 0;

    /**
     * @var int
     */
    public $minLower = 0;

    /**
     * @var int
     */
    public $minNumeric = 0;

    /**
     * @var int
     */
    public $minSpecial = 0;

    /**
     * @var string
     */
    public $specialChars = '.,:;!?:+-*#\\/|(){}[]';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
