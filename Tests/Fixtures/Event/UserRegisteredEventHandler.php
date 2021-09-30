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

use SoureCode\Component\Cqrs\EventHandlerInterface;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\Email;
use SoureCode\Component\Cqrs\Tests\Fixtures\Model\User;
use SoureCode\Component\Cqrs\Tests\Fixtures\Store;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class UserRegisteredEventHandler implements EventHandlerInterface
{
    public Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __invoke(UserRegisteredEvent $event)
    {
        $id = $event->getId();

        /**
         * @var User $user
         */
        $user = $this->store->get($id);

        // Actually you would send an email here, but to track the state we just create an email.
        $email = new Email('Hello '.$user->getName().'!');
        $email->setId(new Ulid());

        $this->store->persist($email);
    }
}
