<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs\Tests\Fixtures\Query;

use Exception;
use SoureCode\Component\Cqrs\QueryHandlerInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class FooQueryHandler implements QueryHandlerInterface
{
    public function __invoke(FooQuery $query)
    {
        $name = $query->getName();

        if ('foo' !== $name) {
            throw new Exception('Something went wrong.');
        }

        return 'bar';
    }
}
