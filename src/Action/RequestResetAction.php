<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Form\Type\RequestPasswordFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RequestResetAction
{
    private Environment $twig;

    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->twig        = $twig;
        $this->formFactory = $formFactory;
        $this->router      = $router;
    }

    public function __invoke(): Response
    {
        $form = $this->formFactory
            ->create(RequestPasswordFormType::class, null, [
                'action' => $this->router->generate('nucleos_user_resetting_send_email'),
                'method' => 'POST',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'resetting.request.submit',
            ])
        ;

        return new Response($this->twig->render('@NucleosUser/Resetting/request.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
