<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Form\Type;

use Nucleos\UserBundle\Form\Model\Resetting;
use Nucleos\UserBundle\Form\Type\ResettingFormType;
use Nucleos\UserBundle\Model\UserInterface;

final class ResettingFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $model = new Resetting(self::createStub(UserInterface::class));

        $form     = $this->factory->create(ResettingFormType::class, $model);
        $formData = [
            'plainPassword' => [
                'first'  => 'test',
                'second' => 'test',
            ],
        ];
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($model, $form->getData());
        self::assertSame('test', $model->getPlainPassword());
    }

    protected function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            new ResettingFormType(Resetting::class),
        ]);
    }
}
