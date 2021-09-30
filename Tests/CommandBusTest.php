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
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommand;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommandHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class CommandBusTest extends TestCase
{

    public function testDispatch(): void
    {
        // Arrange
        $store = new Store();

        $eventMessageBus = new MessageBus([
            new HandleMessageMiddleware(
                new HandlersLocator([
                ]), true
            ),
        ]);

        $eventBus = new EventBus($eventMessageBus);

        $messageBus = new MessageBus([
            new HandleMessageMiddleware(
                new HandlersLocator([
                    RegisterUserCommand::class => [new RegisterUserCommandHandler($store, $eventBus)],
                ])
            ),
        ]);

        $commandBus = new CommandBus($messageBus);

        $id = new Ulid();
        $command = new RegisterUserCommand($id, 'foo');

        // Act
        $commandBus->dispatch($command);

        // Assert
        self::assertSame($store->get($id)->getName(), 'foo');
    }

}
