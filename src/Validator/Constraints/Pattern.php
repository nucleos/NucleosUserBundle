<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
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

    public string $specialChars = '.,:;!?:+-*#\/|(){}[]';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        mixed $options = null,
        ?int $minUpper = null,
        ?int $minLower = null,
        ?int $minNumeric = null,
        ?int $minSpecial = null,
        ?string $specialChars = null,
        ?string $minUpperMessage = null,
        ?string $minLowerMessage = null,
        ?string $minNumericMessage = null,
        ?string $minSpecialMessage = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->minUpper          = $minUpper                   ?? $this->minUpper;
        $this->minLower          = $minLower                   ?? $this->minLower;
        $this->minNumeric        = $minNumeric                 ?? $this->minNumeric;
        $this->minSpecial        = $minSpecial                 ?? $this->minSpecial;
        $this->minUpperMessage   = $minUpperMessage            ?? $this->minUpperMessage;
        $this->minLowerMessage   = $minLowerMessage            ?? $this->minLowerMessage;
        $this->minNumericMessage = $minNumericMessage          ?? $this->minNumericMessage;
        $this->minSpecialMessage = $minSpecialMessage          ?? $this->minSpecialMessage;
        $this->specialChars      = $specialChars               ?? $this->specialChars;
    }
}
