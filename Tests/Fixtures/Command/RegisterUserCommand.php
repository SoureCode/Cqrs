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

use SoureCode\Component\Cqrs\CommandInterface;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class RegisterUserCommand implements CommandInterface
{
    private Ulid $id;
    private string $name;

    public function __construct(Ulid $id, string $name)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
