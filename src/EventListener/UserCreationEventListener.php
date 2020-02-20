<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\User\UserCreatedEvent;
use App\Event\User\UserRegisteredEvent;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

/**
 * @help Not final because we should be able to mock this for testing
 */
class UserCreationEventListener
{
    private $twig;

    private $mailer;

    private $mailerFromAddress;

    public function __construct(Environment $twig, Swift_Mailer $mailer, string $mailerFromAddress)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->mailerFromAddress = $mailerFromAddress;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();

        $message = (new Swift_Message('Registration confirmation'))
            ->setFrom($this->mailerFromAddress)
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('emails/security/registration.html.twig', ['user' => $user]), 'text/html');

        $this->mailer->send($message);
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $user = $event->getUser();
        $password = $event->getPassword();

        $message = (new Swift_Message('Account creation confirmation'))
            ->setFrom($this->mailerFromAddress)
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('emails/admin/user_created.html.twig', ['user' => $user, 'password' => $password]), 'text/html');

        $this->mailer->send($message);
    }
}
