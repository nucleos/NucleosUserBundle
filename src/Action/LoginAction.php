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

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Event\GetResponseLoginEvent;
use Nucleos\UserBundle\Form\Type\LoginFormType;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class LoginAction
{
    private Environment $twig;

    private EventDispatcherInterface $eventDispatcher;

    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->twig             = $twig;
        $this->eventDispatcher  = $eventDispatcher;
        $this->formFactory      = $formFactory;
        $this->router           = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __invoke(Request $request): Response
    {
        $event = new GetResponseLoginEvent($request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::SECURITY_LOGIN_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory
            ->create(LoginFormType::class, null, [
                'action' => $this->router->generate('nucleos_user_security_check'),
                'method' => 'POST',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'security.login.submit',
            ])
        ;

        $error = null;
        if ($form->getErrors()->count() > 0) {
            $error = $form->getErrors()->current()->getMessage();
        }

        return new Response($this->twig->render('@NucleosUser/Security/login.html.twig', [
            'form'          => $form->createView(),
            // TODO: Remove this fields with the next major release
            'last_username' => $this->getLastUsername($form),
            'error'         => $error,
            'csrf_token'    => $this->csrfTokenManager->getToken('authenticate'),
        ]));
    }

    private function getLastUsername(FormInterface $form): ?string
    {
        $data = $form->getData();

        return $data['_username'] ?? null;
    }
}
