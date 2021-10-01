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

use Doctrine\Common\Collections\ArrayCollection;
use SoureCode\Component\Cqrs\QueryHandlerInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class GetUserQueryHandler implements QueryHandlerInterface
{
    private ArrayCollection $collection;

    public function __construct(ArrayCollection $collection)
    {
        $this->collection = $collection;
    }

    public function __invoke(GetUserQuery $query)
    {
        $id = $query->getId();

        return $this->collection->get($id->toRfc4122());
    }
}
