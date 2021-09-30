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
use SoureCode\Component\Cqrs\CommandBus;
use SoureCode\Component\Cqrs\EventBus;
use SoureCode\Component\Cqrs\QueryBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommand;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommandHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEventHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQuery;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQueryHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class IntegrationTest extends TestCase
{
    public function testUserRegistration(): void
    {
        // Arrange
        $store = new Store();

        $eventMessageBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                UserRegisteredEvent::class => [new UserRegisteredEventHandler($store)],
            ])),
        ]);

        $eventBus = new EventBus($eventMessageBus);

        $commandMessageBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                RegisterUserCommand::class => [new RegisterUserCommandHandler($store, $eventBus)],
            ])),
        ]);

        $commandBus = new CommandBus($commandMessageBus);

        $queryMessageBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                GetUserQuery::class => [new GetUserQueryHandler($store)],
            ])),
        ]);

        $queryBus = new QueryBus($queryMessageBus);

        // Act
        $id = $commandBus->dispatch(new RegisterUserCommand('Jason'));
        $user = $queryBus->handle(new GetUserQuery($id));

        // Assert
        self::assertSame($user->getName(), 'Jason');
        self::assertSame($user->getId(), 0);
        self::assertTrue($store->has(0), 'Store contains the user');
        self::assertTrue($store->has(1), 'Store contains the email');

        $email = $store->get(1);
        self::assertSame($email->getContent(), 'Hello Jason!');
    }
}
