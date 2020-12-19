<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

final class LoginFormType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'security.login.username',
                'attr'  => [
                    'autocomplete' => 'username',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'security.login.password',
                'attr'  => [
                    'autocomplete' => 'password',
                ],
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label'    => 'security.login.remember_me',
                'required' => false,
                'value'    => 'on',
            ])
            ->add('_target_path', HiddenType::class)
            ->add('save', SubmitType::class, [
                'label' => 'security.login.submit',
            ])
        ;

        $request = $this->requestStack->getCurrentRequest();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($request): void {
            if (null === $request) {
                return;
            }

            $error = null;

            if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
            } else {
                $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
            }

            if (null !== $error) {
                $event->getForm()->addError(new FormError($error->getMessage()));
            }

            $event->setData(array_replace((array) $event->getData(), [
                'username' => $request->getSession()->get(Security::LAST_USERNAME),
            ]));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'NucleosUserBundle',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
