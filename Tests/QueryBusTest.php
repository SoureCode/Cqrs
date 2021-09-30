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
use SoureCode\Component\Cqrs\QueryBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQuery;
use SoureCode\Component\Cqrs\Tests\Fixtures\Query\GetUserQueryHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class QueryBusTest extends TestCase
{
    public function testHandle(): void
    {
        // Arrange
        $store = new Store();
        $id = new Ulid();

        $user = new User();
        $user->setId($id);
        $user->setName('bar');

        $store->persist($user);

        $queryHandler = new GetUserQueryHandler($store);
        $query = new GetUserQuery($id);

        $messageBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                GetUserQuery::class => [$queryHandler],
            ])),
        ]);
        $queryBus = new QueryBus($messageBus);

        // Act
        $actual = $queryBus->handle($query);

        // Assert
        self::assertSame($actual, $user);
    }
}
