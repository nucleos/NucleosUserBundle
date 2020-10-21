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

namespace Nucleos\UserBundle\Tests\Util;

use Generator;
use Nucleos\UserBundle\Util\Canonicalizer;
use PHPUnit\Framework\TestCase;

final class CanonicalizerTest extends TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     */
    public function testCanonicalize(string $source, string $expectedResult): void
    {
        $canonicalizer = new Canonicalizer();
        static::assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    /**
     * @phpstan-return Generator<array{string, string}>
     */
    public function canonicalizeProvider(): Generator
    {
        yield ['FOO', 'foo'];
        yield [\chr(171), \PHP_VERSION_ID < 50600 ? \chr(171) : '?'];
    }
}
