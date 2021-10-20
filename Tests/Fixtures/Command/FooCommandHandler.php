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

use Exception;
use SoureCode\Component\Cqrs\CommandHandlerInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class FooCommandHandler implements CommandHandlerInterface
{
    public function __invoke(FooCommand $command)
    {
        $name = $command->getName();

        if ('foo' !== $name) {
            throw new Exception('Something went wrong.');
        }
    }
}
