<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class PatternValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Pattern) {
            throw new UnexpectedTypeException($constraint, Pattern::class);
        }

        if (null === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $this->validateUpper($constraint, $value);
        $this->validateLower($constraint, $value);
        $this->validateNumeric($constraint, $value);
        $this->validateSpecial($constraint, $value);
    }

    private function validateUpper(Pattern $constraint, string $value): void
    {
        if ($constraint->minUpper <= 0) {
            return;
        }
        if ($this->countMatches('/[A-Z]/', $value) < $constraint->minUpper) {
            $this->context
                ->buildViolation($constraint->minUpperMessage, [
                    '{{ count }}' => $constraint->minUpper,
                ])
                ->addViolation()
            ;
        }
    }

    private function validateLower(Pattern $constraint, string $value): void
    {
        if ($constraint->minLower <= 0) {
            return;
        }
        if ($this->countMatches('/[a-z]/', $value) < $constraint->minLower) {
            $this->context
                ->buildViolation($constraint->minLowerMessage, [
                    '{{ count }}' => $constraint->minLower,
                ])
                ->addViolation()
            ;
        }
    }

    private function validateNumeric(Pattern $constraint, string $value): void
    {
        if ($constraint->minNumeric <= 0) {
            return;
        }
        if ($this->countMatches('/[\d]/', $value) < $constraint->minNumeric) {
            $this->context
                ->buildViolation($constraint->minNumericMessage, [
                    '{{ count }}' => $constraint->minNumeric,
                ])
                ->addViolation()
            ;
        }
    }

    private function validateSpecial(Pattern $constraint, string $value): void
    {
        if ($constraint->minSpecial <= 0) {
            return;
        }

        if ($this->countMatches('/['.preg_quote($constraint->specialChars, '/').']/', $value) < $constraint->minSpecial) {
            $this->context
                ->buildViolation($constraint->minSpecialMessage, [
                    '{{ count }}' => $constraint->minSpecial,
                    '{{ chars }}' => $constraint->specialChars,
                ])
                ->addViolation()
            ;
        }
    }

    private function countMatches(string $pattern, string $text): int
    {
        $result = preg_match_all($pattern, $text);

        return \is_int($result) ? $result : 0;
    }
}
