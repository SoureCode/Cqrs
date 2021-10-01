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

use SoureCode\Component\Cqrs\CommandBus;
use SoureCode\Component\Cqrs\EventBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommand;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommandHandler;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class CommandBusTest extends AbstractCqrsTestCase
{
    public function testDispatch(): void
    {
        // Arrange
        $collection = $this->createCollection();
        $eventMessageBus = $this->createMessageBus([], true);
        $commandMessageBus = $this->createMessageBus([
            RegisterUserCommand::class => [new RegisterUserCommandHandler($collection)],
        ]);

        $eventBus = new EventBus($eventMessageBus);
        $commandBus = new CommandBus($commandMessageBus, $eventBus);

        $id = new Ulid();

        // Act
        $commandBus->dispatch(new RegisterUserCommand($id, 'foo'));

        // Assert
        self::assertSame($collection->get($id->toRfc4122())->getName(), 'foo');

        self::assertCount(1, $commandMessageBus->getDispatchedMessages());
        self::assertCount(1, $eventMessageBus->getDispatchedMessages());
    }
}
