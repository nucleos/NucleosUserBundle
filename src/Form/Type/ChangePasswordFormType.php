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

namespace Nucleos\UserBundle\Form\Type;

use Nucleos\UserBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ChangePasswordFormType extends AbstractType
{
    /**
     * @phpstan-var class-string<ChangePassword>
     */
    private string $class;

    /**
     * @phpstan-param class-string<ChangePassword> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $constraintsOptions = [
            'message' => 'nucleos_user.current_password.invalid',
        ];

        if (null !== $options['validation_groups']) {
            $constraintsOptions['groups'] = [reset($options['validation_groups'])];
        }

        $builder
            ->add('current_password', PasswordType::class, [
                'label'              => 'form.current_password',
                'mapped'             => false,
                'constraints'        => [
                    new NotBlank(),
                    new UserPassword($constraintsOptions),
                ],
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'    => PasswordType::class,
                'options' => [
                    'attr'               => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options'   => ['label' => 'form.new_password'],
                'second_options'  => ['label' => 'form.new_password_confirmation'],
                'invalid_message' => 'nucleos_user.password.mismatch',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => $this->class,
            'csrf_token_id'      => 'change_password',
            'translation_domain' => 'NucleosUserBundle',
        ]);
    }
}
