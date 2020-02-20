<?php

declare(strict_types=1);

namespace App\Action\Security;

use App\Form\Type\Security\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class LoginAction
{
    private $twig;

    private $formFactory;

    private $authenticationUtils;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory, AuthenticationUtils $authenticationUtils)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function __invoke()
    {
        $loginForm = $this->formFactory->create(LoginType::class);

        return new Response(
            $this->twig->render('security/login.html.twig', [
                'error' => $this->authenticationUtils->getLastAuthenticationError(),
                'lastUsername' => $this->authenticationUtils->getLastUsername(),
                'loginForm' => $loginForm->createView(),
            ])
        );
    }
}
