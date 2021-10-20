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

use SoureCode\Component\Cqrs\QueryBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\FooQuery;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\FooQueryHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQuery;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQueryHandler;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class QueryBusTest extends AbstractCqrsTestCase
{
    public function testExceptionHandling(): void
    {
        // Assert
        $this->expectExceptionMessage('Something went wrong.');

        // Arrange
        $messageBus = $this->createMessageBus([
            FooQuery::class => [new FooQueryHandler()],
        ]);

        $queryBus = new QueryBus($messageBus);

        // Act
        $queryBus->handle(new FooQuery('bar'));
    }

    public function testHandle(): void
    {
        // Arrange
        $id = new Ulid();
        $user = $this->createUser($id, 'foo');
        $collection = $this->createCollection($user);

        $messageBus = $this->createMessageBus([
            GetUserQuery::class => [new GetUserQueryHandler($collection)],
        ]);

        $queryBus = new QueryBus($messageBus);

        // Act
        $actual = $queryBus->handle(new GetUserQuery($id));

        // Assert
        self::assertSame($actual, $user);
        self::assertCount(1, $messageBus->getDispatchedMessages());
    }
}
