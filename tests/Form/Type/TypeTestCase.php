<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;

abstract class TypeTestCase extends BaseTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypes($this->getTypes())
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->getFormFactory()
        ;

        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    /**
     * @return FormTypeExtensionInterface<mixed>[]
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return FormTypeInterface<mixed>[]
     */
    protected function getTypes(): array
    {
        return [];
    }
}
