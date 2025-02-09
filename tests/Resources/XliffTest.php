<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Resources;

use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\XliffFileLoader;

final class XliffTest extends TestCase
{
    private readonly XliffFileLoader $loader;

    /**
     * @var string[]
     */
    private array $errors = [];

    protected function setUp(): void
    {
        $this->loader = new XliffFileLoader();
    }

    /**
     * @dataProvider provideXliffCases
     */
    public function testXliff(string $locale): void
    {
        $this->validateXliff($locale);

        if (\count($this->errors) > 0) {
            self::fail(\sprintf('Unable to parse xliff files: %s', implode(', ', $this->errors)));
        }
    }

    /**
     * @return Generator<string[]>
     */
    public static function provideXliffCases(): iterable
    {
        $files = glob(\sprintf('%s/*.xlf', __DIR__.'/../../src/Resources/translations'));

        if (false === $files) {
            return;
        }

        foreach ($files as $file) {
            yield $file => [$file];
        }
    }

    private function validateXliff(string $file): void
    {
        [,$locale] = explode('.', $file);

        try {
            $catalogue = $this->loader->load($file, $locale);

            self::assertGreaterThan(0, $catalogue->getResources());
        } catch (InvalidResourceException $e) {
            $this->errors[] = \sprintf('%s => %s', $locale, $e->getMessage());
        }
    }
}
