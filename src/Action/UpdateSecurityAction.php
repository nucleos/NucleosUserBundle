<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Event\FilterUserResponseEvent;
use Nucleos\UserBundle\Event\FormEvent;
use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Form\Type\UpdateSecurityFormType;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Util\UserManipulator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class UpdateSecurityAction
{
    private readonly Environment $twig;

    private readonly RouterInterface $router;

    private readonly Security $security;

    private readonly EventDispatcherInterface $eventDispatcher;

    private readonly FormFactoryInterface $formFactory;

    private readonly UserManipulator $userManipulator;

    private readonly UserManager $userManager;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        Security $security,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        UserManipulator $userManipulator,
        UserManager $userManager
    ) {
        $this->twig             = $twig;
        $this->router           = $router;
        $this->security         = $security;
        $this->eventDispatcher  = $eventDispatcher;
        $this->formFactory      = $formFactory;
        $this->userManipulator  = $userManipulator;
        $this->userManager      = $userManager;
    }

    /**
     * @SuppressWarnings("PHPMD.NPathComplexity")
     */
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();

        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::UPDATE_SECURITY_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, NucleosUserEvents::UPDATE_SECURITY_SUCCESS);

            $this->updatePassword($user);
            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $response = new RedirectResponse($this->router->generate('nucleos_user_update_security'));
            }

            $this->eventDispatcher->dispatch(new FilterUserResponseEvent($user, $request, $response), NucleosUserEvents::UPDATE_SECURITY_COMPLETED);

            return $response;
        }

        return new Response($this->twig->render('@NucleosUser/UpdateSecurity/update_security.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    private function createForm(UserInterface $model): FormInterface
    {
        return $this->formFactory
            ->create(UpdateSecurityFormType::class, $model, [
                'validation_groups' => ['UpdateSecurity', 'Default'],
            ])
            ->add('save', SubmitType::class, [
                'label'  => 'update_security.submit',
            ])
        ;
    }

    private function updatePassword(UserInterface $user): void
    {
        if (null === $user->getPlainPassword()) {
            return;
        }

        $this->userManipulator->changePassword($user->getUsername(), $user->getPlainPassword());
    }
}
