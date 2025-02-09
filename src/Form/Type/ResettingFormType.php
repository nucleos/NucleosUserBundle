<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Form\Type;

use Nucleos\UserBundle\Form\Model\Resetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResettingFormType extends AbstractType
{
    /**
     * @phpstan-var class-string<Resetting>
     */
    private readonly string $class;

    /**
     * @phpstan-param class-string<Resetting> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'options'         => [
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
            'csrf_token_id'      => 'resetting',
            'translation_domain' => 'NucleosUserBundle',
        ]);
    }
}
