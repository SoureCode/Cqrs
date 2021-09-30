<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs\Tests\Fixtures;

use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Store
{

    private array $items = [];

    public function get(Ulid $id): mixed
    {
        if (!$this->has($id)) {
            throw new \Exception(sprintf("Item with id \"%d\" not found", $id));
        }

        return $this->items[$id->toRfc4122()];
    }

    public function has(Ulid $id): bool
    {
        return array_key_exists($id->toRfc4122(), $this->items);
    }

    public function getAll(): array
    {
        return $this->items;
    }

    public function persist(object $item): void
    {
        if (!method_exists($item, 'getId')) {
            throw new \Exception("Missing getId method.");
        }

        $id = $item->getId();
        $this->items[$id->toRfc4122()] = $item;
    }

    public function update(object $item): void
    {
        if (!method_exists($item, 'getId')) {
            throw new \Exception("Missing getId method.");
        }

        $id = $item->getId();

        if (!$this->has($id)) {
            throw new \Exception(sprintf("Item with id \"%d\" not found", $id));
        }

        $this->items[$id->toRfc4122()] = $item;
    }

}
