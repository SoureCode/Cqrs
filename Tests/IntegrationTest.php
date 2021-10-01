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
use SoureCode\Component\Cqrs\QueryBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommand;
use SoureCode\Component\Cqrs\Tests\Fixtures\Command\RegisterUserCommandHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEventHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\Email;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQuery;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQueryHandler;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class IntegrationTest extends AbstractCqrsTestCase
{
    public function testUserRegistration(): void
    {
        // Arrange
        $collection = $this->createCollection();

        $eventMessageBus = $this->createMessageBus([
            UserRegisteredEvent::class => [new UserRegisteredEventHandler($collection)],
        ]);

        $commandMessageBus = $this->createMessageBus([
            RegisterUserCommand::class => [new RegisterUserCommandHandler($collection)],
        ]);

        $queryMessageBus = $this->createMessageBus([
            GetUserQuery::class => [new GetUserQueryHandler($collection)],
        ]);

        $eventBus = new EventBus($eventMessageBus);
        $commandBus = new CommandBus($commandMessageBus, $eventBus);
        $queryBus = new QueryBus($queryMessageBus);

        $id = new Ulid();

        // Act
        $commandBus->dispatch(new RegisterUserCommand($id, 'Jason'));
        $user = $queryBus->handle(new GetUserQuery($id));

        // Assert
        self::assertCount(1, $commandMessageBus->getDispatchedMessages());
        self::assertCount(1, $eventMessageBus->getDispatchedMessages());
        self::assertCount(1, $queryMessageBus->getDispatchedMessages());

        self::assertSame($user->getName(), 'Jason');
        self::assertSame($user->getId()->toRfc4122(), $id->toRfc4122());
        self::assertCount(2, $collection);

        /**
         * @var Email $email
         */
        $email = $collection->last();

        self::assertSame($email->getContent(), 'Hello Jason!');
    }
}
