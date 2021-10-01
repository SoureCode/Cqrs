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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\TraceableMessageBus;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
abstract class AbstractCqrsTestCase extends TestCase
{
    protected function createCollection(?User $user = null): ArrayCollection
    {
        $collection = new ArrayCollection();

        if (null !== $user) {
            $collection->set($user->getId()->toRfc4122(), $user);
        }

        return $collection;
    }

    /**
     * @param HandlerDescriptor[][]|callable[][] $handlers
     */
    protected function createMessageBus(array $handlers, bool $allowNoHandlers = false): TraceableMessageBus
    {
        return new TraceableMessageBus(
            new MessageBus([
                new HandleMessageMiddleware(
                    new HandlersLocator($handlers),
                    $allowNoHandlers
                ),
            ])
        );
    }

    protected function createUser(Ulid $id, string $name): User
    {
        $user = new User($id);

        $user->setName($name);

        return $user;
    }
}
