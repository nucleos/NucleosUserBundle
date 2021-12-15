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

namespace Nucleos\UserBundle\Validator;

use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Symfony\Component\Validator\ObjectInitializerInterface;

final class Initializer implements ObjectInitializerInterface
{
    private CanonicalFieldsUpdater $canonicalFieldsUpdater;

    public function __construct(CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * @param object $object
     */
    public function initialize($object): void
    {
        if ($object instanceof UserInterface) {
            $this->canonicalFieldsUpdater->updateCanonicalFields($object);
        }
    }
}
