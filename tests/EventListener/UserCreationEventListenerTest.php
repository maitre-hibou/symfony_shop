<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\Entity\User;
use App\Event\User\UserCreatedEvent;
use App\Event\User\UserRegisteredEvent;
use App\EventListener\UserCreationEventListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\EventDispatcher\Event;

final class UserCreationEventListenerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testEventListenerIsCalledForUserRegistration()
    {
        $user = new User();
        $event = new UserRegisteredEvent($user);

        $listener = $this->getMockBuilder(UserCreationEventListener::class)
            ->setConstructorArgs([static::$container->get('twig'), static::$container->get('mailer'), static::$container->getParameter('mailerFromAddress')])
            ->getMock();

        $listener->expects($this->once())
            ->method('onUserRegistered')
            ->with($event);

        static::$container->set(UserCreationEventListener::class, $listener);

        $this->triggerEvent($event);
    }

    public function testEventListenerIsCalledForUserCreation()
    {
        $user = new User();
        $event = new UserCreatedEvent($user, 'secret');

        $listener = $this->getListenerMock();

        $listener->expects($this->once())
            ->method('onUserCreated')
            ->with($event);

        static::$container->set(UserCreationEventListener::class, $listener);

        $this->triggerEvent($event);
    }

    private function getListenerMock()
    {
        return $this->getMockBuilder(UserCreationEventListener::class)
            ->setConstructorArgs([static::$container->get('twig'), static::$container->get('mailer'), static::$container->getParameter('mailerFromAddress')])
            ->getMock();
    }

    private function triggerEvent(Event $event)
    {
        $dispatcher = static::$container->get('event_dispatcher');
        $dispatcher->dispatch($event);
    }
}
