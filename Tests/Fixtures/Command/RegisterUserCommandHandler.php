<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs\Tests\Fixtures\Command;

use SoureCode\Component\Cqrs\CommandHandlerInterface;
use SoureCode\Component\Cqrs\EventBusInterface;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class RegisterUserCommandHandler implements CommandHandlerInterface
{

    private EventBusInterface $eventBus;
    private Store $store;

    public function __construct(Store $store, EventBusInterface $eventBus)
    {
        $this->store = $store;
        $this->eventBus = $eventBus;
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $id = $command->getId();

        $user = new User();
        $user->setId($id);
        $user->setName($command->getName());

        $this->store->persist($user);

        $event = new UserRegisteredEvent($id);
        $eventEnvelope = (new Envelope($event))->with(new DispatchAfterCurrentBusStamp());
        $this->eventBus->dispatch($eventEnvelope);
    }
}
