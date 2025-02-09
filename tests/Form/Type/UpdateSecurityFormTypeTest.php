<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Form\Type;

use Nucleos\UserBundle\Form\Type\UpdateSecurityFormType;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;

final class UpdateSecurityFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $model = new TestUser();
        $model->setPlainPassword('foo');

        $form     = $this->factory->create(UpdateSecurityFormType::class, $model);
        $formData = [
            'current_password' => 'foo',
            'plainPassword'    => [
                'first'  => 'bar',
                'second' => 'bar',
            ],
        ];
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($model, $form->getData());
        self::assertSame('bar', $model->getPlainPassword());
    }

    protected function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            new UpdateSecurityFormType(TestUser::class),
        ]);
    }
}
