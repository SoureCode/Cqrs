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

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class EventBus implements EventBusInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(EventInterface $event, array $stamps = []): void
    {
        $envelope = Envelope::wrap($event, $stamps);

        $this->messageBus->dispatch($envelope);
    }
}
