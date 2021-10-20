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
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Throwable;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class CommandBus implements CommandBusInterface
{
    use HandleTrait {
        handle as private handleCommand;
    }

    private EventBusInterface $eventBus;

    public function __construct(MessageBusInterface $messageBus, EventBusInterface $eventBus)
    {
        $this->messageBus = $messageBus;
        $this->eventBus = $eventBus;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(CommandInterface $command, array $stamps = []): void
    {
        try {
            $envelope = Envelope::wrap($command, $stamps);

            $events = $this->handleCommand($envelope);
        } catch (HandlerFailedException $exception) {
            while ($exception instanceof HandlerFailedException) {
                /**
                 * @var Throwable $exception
                 */
                $exception = $exception->getPrevious();
            }

            throw $exception;
        }

        if ($events instanceof Generator) {
            $this->dispatchEvents($events);
        }
    }

    /**
     * @param Generator<mixed> $events
     */
    protected function dispatchEvents(Generator $events): void
    {
        foreach ($events as $event) {
            if ($event instanceof EventInterface) {
                $this->eventBus->dispatch($event, [
                    new DispatchAfterCurrentBusStamp(),
                ]);
            }
        }
    }
}
