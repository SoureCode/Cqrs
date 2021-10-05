<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs;

use Generator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class CommandBus implements CommandBusInterface
{
    use HandleTrait {
        handle as handleCommand;
    }

    private EventBusInterface $eventBus;

    public function __construct(EventBusInterface $eventBus, MessageBusInterface $messageBus)
    {
        $this->eventBus = $eventBus;
        $this->messageBus = $messageBus;
    }

    public function dispatch(CommandInterface|Envelope $command): void
    {
        $events = $this->handleCommand($command);

        if ($events instanceof Generator) {
            foreach ($events as $event) {
                if ($event instanceof EventInterface) {
                    $envelope = Envelope::wrap($event, [new DispatchAfterCurrentBusStamp()]);

                    $this->eventBus->dispatch($envelope);
                }
            }
        }
    }
}
