<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Form\Type;

use Nucleos\UserBundle\Form\Model\AccountDeletion;
use Nucleos\UserBundle\Form\Type\AccountDeletionFormType;

final class AccountDeletionFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $model = new AccountDeletion();

        $form     = $this->factory->create(AccountDeletionFormType::class, $model);
        $formData = [
            'current_password' => 'foo',
            'confirm'          => true,
        ];
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($model, $form->getData());
        self::assertTrue($model->isConfirm());
    }

    protected function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            new AccountDeletionFormType(AccountDeletion::class),
        ]);
    }
}
