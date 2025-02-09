<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LoginFormType extends AbstractType
{
    private readonly AuthenticationUtils $authenticationUtils;

    private readonly TranslatorInterface $translator;

    public function __construct(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->translator          = $translator;
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
            ->add('_target_path', HiddenType::class)
        ;

        $authenticationUtils = $this->authenticationUtils;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($authenticationUtils): void {
            $error = $authenticationUtils->getLastAuthenticationError();
            if (null !== $error) {
                $message = $this->translator->trans($error->getMessageKey(), [], 'security');

                $event->getForm()->addError(new FormError($message));
            }

            $event->setData(array_replace((array) $event->getData(), [
                'username' => $authenticationUtils->getLastUsername(),
            ]));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'NucleosUserBundle',
            'csrf_field_name'    => '_csrf_token',
            'csrf_token_id'      => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
