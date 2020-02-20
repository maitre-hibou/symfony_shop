<?php

declare(strict_types=1);

namespace App\Action\Security;

use App\Form\Dto\Security\ResetPassword;
use App\Form\Type\Security\ResetPasswordType;
use App\Security\ResetPasswordHandler;
use LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class ResetPasswordAction
{
    private $twig;

    private $formFactory;

    private $router;

    private $resetPasswordHandler;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        ResetPasswordHandler $resetPasswordHandler
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->resetPasswordHandler = $resetPasswordHandler;
    }

    public function __invoke(Request $request, Session $session, string $token)
    {
        try {
            $resetPasswordRequest = $this->resetPasswordHandler->getResetPasswordRequest($token);
        } catch (LogicException $e) {
            $session->getFlashBag()->add('error', $e->getMessage());

            return new RedirectResponse($this->router->generate('security_reset_password_request'));
        }

        $resetPasswordForm = $this->formFactory->create(ResetPasswordType::class);

        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            /** @var ResetPassword $resetPassword */
            $resetPassword = $resetPasswordForm->getData();

            try {
                $this->resetPasswordHandler->handleUserPasswordChange($resetPasswordRequest, $resetPassword);
            } catch (LogicException $e) {
                $session->getFlashBag()->add('error', $e->getMessage());

                return new RedirectResponse($this->router->generate('security_reset_password_request'));
            }

            $session->getFlashBag()->add('success', 'Your password has been updated successfully.');

            return new RedirectResponse($this->router->generate('security_login'));
        }

        return new Response(
            $this->twig->render('security/reset_password.html.twig', [
                'resetPasswordForm' => $resetPasswordForm->createView(),
            ])
        );
    }
}
