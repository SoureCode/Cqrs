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

use Doctrine\Common\Collections\ArrayCollection;
use SoureCode\Component\Cqrs\CommandHandlerInterface;
use SoureCode\Component\Cqrs\Tests\Fixtures\Event\UserRegisteredEvent;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class RegisterUserCommandHandler implements CommandHandlerInterface
{
    private ArrayCollection $collection;

    public function __construct(ArrayCollection $collection)
    {
        $this->collection = $collection;
    }

    public function __invoke(RegisterUserCommand $command)
    {
        $id = $command->getId();

        $user = new User($id);

        $user->setName($command->getName());

        $this->collection->set($id->toRfc4122(), $user);

        return yield new UserRegisteredEvent($id);
    }
}
