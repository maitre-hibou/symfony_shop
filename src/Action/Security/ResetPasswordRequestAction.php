<?php

declare(strict_types=1);

namespace App\Action\Security;

use App\Form\Dto\Security\ResetPasswordRequest;
use App\Form\Type\Security\ResetPasswordRequestType;
use App\Security\ResetPasswordHandler;
use Swift_Mailer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class ResetPasswordRequestAction
{
    private $twig;

    private $formFactory;

    private $router;

    private $mailer;

    private $resetPasswordHandler;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        ResetPasswordHandler $resetPasswordHandler,
        Swift_Mailer $mailer
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->resetPasswordHandler = $resetPasswordHandler;
        $this->mailer = $mailer;
    }

    public function __invoke(Request $request, Session $session)
    {
        $resetPasswordRequestForm = $this->formFactory->create(ResetPasswordRequestType::class);

        $resetPasswordRequestForm->handleRequest($request);

        if ($resetPasswordRequestForm->isSubmitted() && $resetPasswordRequestForm->isValid()) {
            /** @var ResetPasswordRequest $resetPasswordRequest */
            $resetPasswordRequest = $resetPasswordRequestForm->getData();

            if (null !== ($resetPasswordRequestEntity = $this->resetPasswordHandler->handleResetPasswordRequest($resetPasswordRequest))) {
                $this->sendEmail($resetPasswordRequestEntity);
            }

            $session->getFlashBag()->add('success', 'Please check your email for further instructions on how to reset your password.');

            return new RedirectResponse($this->router->generate('homepage'));
        }

        return new Response(
            $this->twig->render('security/reset_password_request.html.twig', [
                'resetPasswordRequestForm' => $resetPasswordRequestForm->createView(),
            ])
        );
    }

    private function sendEmail(\App\Entity\ResetPasswordRequest $resetPasswordRequestEntity)
    {
        $message = (new \Swift_Message('Reset your password'))
            ->setFrom('noreply@symfony_core.local')
            ->setTo($resetPasswordRequestEntity->getEmail())
            ->setBody($this->twig->render('emails/security/reset_password_request.html.twig', ['resetPasswordRequest' => $resetPasswordRequestEntity]), 'text/html');

        $this->mailer->send($message);
    }
}
