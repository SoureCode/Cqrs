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

use SoureCode\Component\Cqrs\EventBus;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEventHandler;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\Email;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class EventBusTest extends AbstractCqrsTestCase
{
    public function testDispatch(): void
    {
        // Arrange
        $id = new Ulid();
        $user = $this->createUser($id, 'lorem');
        $collection = $this->createCollection($user);

        $messageBus = $this->createMessageBus([
            UserRegisteredEvent::class => [new UserRegisteredEventHandler($collection)],
        ]);

        $eventBus = new EventBus($messageBus);

        // Act
        $eventBus->dispatch(new UserRegisteredEvent($id));

        // Assert
        /**
         * @var Email $email
         */
        $email = $collection->last();

        self::assertTrue($collection->containsKey($id->toRfc4122()));
        self::assertSame($email->getContent(), 'Hello lorem!');
        self::assertCount(1, $messageBus->getDispatchedMessages());
    }
}
