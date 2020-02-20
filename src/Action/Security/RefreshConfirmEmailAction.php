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
use Twig\Environment;

final class RefreshConfirmEmailAction
{
    private $emailConfirmationTokenRepository;

    private $router;

    private $twig;

    private $mailer;

    private $entityManager;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        \Swift_Mailer $mailer,
        EmailConfirmationTokenRepository $emailConfirmationTokenRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->emailConfirmationTokenRepository = $emailConfirmationTokenRepository;
        $this->router = $router;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, Session $session, string $token)
    {
        /** @var EmailConfirmationToken|null $token */
        $token = $this->emailConfirmationTokenRepository->findOneBy(['token' => $token]);

        if (null === $token) {
            $session->getFlashBag()->add('error', 'This token does not exists.');

            return new RedirectResponse($this->router->generate('homepage'));
        }

        $token->setToken(sha1(uniqid($token->getUser()->getEmail(), true)))
            ->setCreatedAt(new \DateTime());

        $this->entityManager->flush();

        $this->sendConfirmationEmail($token->getUser());

        $session->getFlashBag()->add('success', sprintf('You are now registered. Please check your mailbox "%s" to confirm your email address.', $token->getUser()->getEmail()));

        return new RedirectResponse($this->router->generate('homepage'));
    }

    private function sendConfirmationEmail(User $user)
    {
        $message = (new \Swift_Message('Registration confirmation'))
            ->setFrom('noreply@symfony_core.local')
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('emails/security/registration.html.twig', ['user' => $user]), 'text/html');

        $this->mailer->send($message);
    }
}
