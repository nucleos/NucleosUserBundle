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

namespace Nucleos\UserBundle\Util;

final class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize(?string $string): string
    {
        if (null === $string) {
            return '';
        }

        $encodings = mb_detect_order();

        if (!\is_array($encodings)) {
            return $string;
        }

        $encoding  = mb_detect_encoding($string, $encodings, true);

        if (false !== $encoding) {
            return mb_convert_case($string, MB_CASE_LOWER, $encoding);
        }

        return mb_convert_case($string, MB_CASE_LOWER);
    }
}
