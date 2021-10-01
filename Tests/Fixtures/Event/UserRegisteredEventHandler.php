<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Cqrs\Tests\Fixtures\Event;

use Doctrine\Common\Collections\ArrayCollection;
use SoureCode\Component\Cqrs\EventHandlerInterface;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\Email;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class UserRegisteredEventHandler implements EventHandlerInterface
{
    private ArrayCollection $collection;

    public function __construct(ArrayCollection $collection)
    {
        $this->collection = $collection;
    }

    public function __invoke(UserRegisteredEvent $event)
    {
        $id = $event->getId();

        /**
         * @var User $user
         */
        $user = $this->collection->get($id->toRfc4122());

        // Actually you would send an email here, but to track the state we just create a custom email model.
        $email = new Email('Hello '.$user->getName().'!');

        $this->collection->add($email);
    }
}
