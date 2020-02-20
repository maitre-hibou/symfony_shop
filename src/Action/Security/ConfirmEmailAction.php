<?php

declare(strict_types=1);

namespace App\Action\Security;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Repository\EmailConfirmationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

final class ConfirmEmailAction
{
    private $emailConfirmationTokenRepository;

    private $router;

    private $entityManager;

    public function __construct(
        EmailConfirmationTokenRepository $emailConfirmationTokenRepository,
        RouterInterface $router,
        EntityManagerInterface $entityManager
    ) {
        $this->emailConfirmationTokenRepository = $emailConfirmationTokenRepository;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, Session $session, string $token)
    {
        /** @var EmailConfirmationToken|null $token */
        $token = $this->emailConfirmationTokenRepository->findOneBy(['token' => $token]);

        if (null === $token) {
            return $this->setErrorAndRedirect($session, 'This token does not exists.');
        }

        if (time() - $token->getCreatedAt()->getTimestamp() > 3600) {
            $refreshUrl = $this->router->generate('security_refresh_confirm_email', ['token' => $token->getToken()]);

            return $this->setErrorAndRedirect($session, sprintf('This token is not valid anymore. <a href="%s" class="alert-link">Resend email.</a>', $refreshUrl));
        }

        $user = $token->getUser();

        $this->entityManager->remove($token);

        $user->setStatus(User::STATUS_ACTIVE);

        $this->entityManager->flush();

        $session->getFlashBag()->add('success', sprintf('Your account is now active. You can procede to <a href="%s" class="alert-link">login</a>.', $this->router->generate('security_login')));

        return new RedirectResponse($this->router->generate('homepage'));
    }

    private function setErrorAndRedirect(Session $session, string $message)
    {
        $session->getFlashBag()->add('error', $message);

        return new RedirectResponse($this->router->generate('homepage'));
    }
}
