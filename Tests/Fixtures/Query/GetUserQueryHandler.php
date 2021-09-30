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

use SoureCode\Component\Cqrs\QueryHandlerInterface;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class GetUserQueryHandler implements QueryHandlerInterface
{
    private Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __invoke(GetUserQuery $query)
    {
        $id = $query->getId();

        return $this->store->get($id);
    }
}
