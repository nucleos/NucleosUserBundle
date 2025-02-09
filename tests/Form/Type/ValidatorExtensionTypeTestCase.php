<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorExtensionTypeTestCase extends TypeTestCase
{
    /**
     * @return FormTypeValidatorExtension[]
     */
    protected function getTypeExtensions(): array
    {
        $validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        return [
            new FormTypeValidatorExtension($validator),
        ];
    }
}
