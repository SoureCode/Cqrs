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

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface EventBusInterface
{
    public function dispatch(EventInterface|Envelope $event): void;
}
