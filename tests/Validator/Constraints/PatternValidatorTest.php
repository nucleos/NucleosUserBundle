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

namespace Nucleos\UserBundle\Tests\Validator\Constraints;

use Generator;
use Nucleos\UserBundle\Validator\Constraints\Pattern;
use Nucleos\UserBundle\Validator\Constraints\PatternValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<PatternValidator>
 */
final class PatternValidatorTest extends ConstraintValidatorTestCase
{
    public function testValidateInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $constraint = new Blank();

        $this->validator->validate('Foo', $constraint);
    }

    public function testValidateInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $constraint = new Pattern();

        $this->validator->validate(new stdClass(), $constraint);
    }

    /**
     * @dataProvider provideValidateLowerCases
     */
    public function testValidateLower(string $text, bool $pass): void
    {
        $constraint           = new Pattern();
        $constraint->minLower = 2;

        $this->validator->validate($text, $constraint);

        if ($pass) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->minLowerMessage)
                ->setParameter('{{ count }}', (string) $constraint->minLower)
                ->assertRaised()
            ;
        }
    }

    /**
     * @dataProvider provideValidateUpperCases
     */
    public function testValidateUpper(string $text, bool $pass): void
    {
        $constraint           = new Pattern();
        $constraint->minUpper = 2;

        $this->validator->validate($text, $constraint);

        if ($pass) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->minUpperMessage)
                ->setParameter('{{ count }}', (string) $constraint->minUpper)
                ->assertRaised()
            ;
        }
    }

    /**
     * @dataProvider provideValidateNumericCases
     */
    public function testValidateNumeric(string $text, bool $pass): void
    {
        $constraint               = new Pattern();
        $constraint->minNumeric   = 2;

        $this->validator->validate($text, $constraint);

        if ($pass) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->minNumericMessage)
                ->setParameter('{{ count }}', (string) $constraint->minNumeric)
                ->assertRaised()
            ;
        }
    }

    /**
     * @dataProvider provideValidateSpecialCases
     */
    public function testValidateSpecial(string $text, bool $pass): void
    {
        $constraint               = new Pattern();
        $constraint->minSpecial   = 2;

        $this->validator->validate($text, $constraint);

        if ($pass) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($constraint->minSpecialMessage)
                ->setParameter('{{ count }}', (string) $constraint->minSpecial)
                ->setParameter('{{ chars }}', $constraint->specialChars)
                ->assertRaised()
            ;
        }
    }

    /**
     * @return Generator<mixed[]>
     *
     * @phpstan-return Generator<array{string, bool}>
     */
    public static function provideValidateLowerCases(): iterable
    {
        yield 'Empty' => ['', false];

        yield 'All lower' => ['somelowertext', true];

        yield 'No lower' => ['SOME LOWER TEXT 123', false];

        yield 'One lower' => ['SOME LOWeR TEXT 123', false];

        yield 'Two lower' => ['SOME LOWeR TeXT 123', true];
    }

    /**
     * @return Generator<mixed[]>
     *
     * @phpstan-return Generator<array{string, bool}>
     */
    public static function provideValidateUpperCases(): iterable
    {
        yield 'Empty' => ['', false];

        yield 'All upper' => ['SOMELOWERTEXT', true];

        yield 'No upper' => ['some lower text', false];

        yield 'One upper' => ['some lowEr text 123', false];

        yield 'Two upper' => ['some lowEr tExt 123', true];
    }

    /**
     * @return Generator<mixed[]>
     *
     * @phpstan-return Generator<array{string, bool}>
     */
    public static function provideValidateNumericCases(): iterable
    {
        yield 'Empty' => ['', false];

        yield 'All numbers' => ['151555151', true];

        yield 'No numbers' => ['some generic Text', false];

        yield 'One number' => ['s0me generic Text', false];

        yield 'Two numbers' => ['s0me generic T3xt', true];
    }

    /**
     * @return Generator<mixed[]>
     *
     * @phpstan-return Generator<array{string, bool}>
     */
    public static function provideValidateSpecialCases(): iterable
    {
        yield 'Empty' => ['', false];

        yield 'All special' => ['][}{)(|/\#*-+:?!;:,./', true];

        yield 'No special' => ['some generic Text', false];

        yield 'One special' => ['s?me generic Text', false];

        yield 'Two specials' => ['s#me generic T?xt', true];
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PatternValidator();
    }
}
