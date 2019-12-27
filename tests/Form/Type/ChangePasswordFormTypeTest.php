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

namespace Nucleos\UserBundle\Tests\Form\Type;

use Nucleos\UserBundle\Form\Model\ChangePassword;
use Nucleos\UserBundle\Form\Type\ChangePasswordFormType;

final class ChangePasswordFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $model = new ChangePassword();
        $model->setPlainPassword('foo');

        $form     = $this->factory->create(ChangePasswordFormType::class, $model);
        $formData = [
            'current_password' => 'foo',
            'plainPassword'    => [
                'first'  => 'bar',
                'second' => 'bar',
            ],
        ];
        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame($model, $form->getData());
        static::assertSame('bar', $model->getPlainPassword());
    }

    protected function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            new ChangePasswordFormType(ChangePassword::class),
        ]);
    }
}
