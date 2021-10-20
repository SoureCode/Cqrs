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
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class QueryBus implements QueryBusInterface
{
    use HandleTrait {
         handle as private handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(QueryInterface $query, array $stamps = []): mixed
    {
        try {
            $envelope = Envelope::wrap($query, $stamps);

            return $this->handleQuery($envelope);
        } catch (HandlerFailedException $exception) {
            while ($exception instanceof HandlerFailedException) {
                /**
                 * @var Throwable $exception
                 */
                $exception = $exception->getPrevious();
            }

            throw $exception;
        }
    }
}
