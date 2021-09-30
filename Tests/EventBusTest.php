<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs\Tests;

use PHPUnit\Framework\TestCase;
use SoureCode\Component\Cqrs\EventBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEventHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\Email;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class EventBusTest extends TestCase
{
    public function testDispatch(): void
    {
        // Arrange
        $store = new Store();
        $id = new Ulid();

        $user = new User();
        $user->setId($id);
        $user->setName('lorem');

        $store->persist($user);

        $eventHandler = new UserRegisteredEventHandler($store);
        $event = new UserRegisteredEvent($id);

        $messageBus = new MessageBus([
            new HandleMessageMiddleware(
                new HandlersLocator([
                    UserRegisteredEvent::class => [$eventHandler],
                ])
            ),
        ]);
        $eventBus = new EventBus($messageBus);

        // Act
        $eventBus->dispatch($event);

        // Assert
        self::assertTrue($store->has($id));

        $all = $store->getAll();
        /**
         * @var Email $email
         */
        $email = array_pop($all);

        self::assertSame($email->getContent(), "Hello lorem!");
    }

}
