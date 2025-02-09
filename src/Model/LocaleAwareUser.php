<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

interface LocaleAwareUser
{
    public function setLocale(?string $locale): void;

    public function getLocale(): ?string;

    public function setTimezone(?string $timezone): void;

    public function getTimezone(): ?string;
}
