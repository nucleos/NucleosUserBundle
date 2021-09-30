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
    public string $minUpperMessage = 'nucleos_user.pattern.requires_upper';

    public string $minLowerMessage = 'nucleos_user.pattern.requires_lower';

    public string $minNumericMessage = 'nucleos_user.pattern.requires_numeric';

    public string $minSpecialMessage = 'nucleos_user.pattern.requires_special';

    public int $minUpper = 0;

    public int $minLower = 0;

    public int $minNumeric = 0;

    public int $minSpecial = 0;

    public string $specialChars = '.,:;!?:+-*#\\/|(){}[]';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
