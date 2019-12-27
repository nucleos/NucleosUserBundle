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
     * @return string[][]
     */
    public function canonicalizeProvider(): array
    {
        return [
            ['FOO', 'foo'],
            [\chr(171), \PHP_VERSION_ID < 50600 ? \chr(171) : '?'],
        ];
    }
}
