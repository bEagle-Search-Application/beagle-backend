<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Listener\User\SendEmailVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmailCommand;
use Beagle\Core\Domain\User\Event\UserCreated;
use Beagle\Shared\Bus\CommandBus;
use Beagle\Shared\Bus\Event;
use Beagle\Shared\Bus\EventListener;

final class SendEmailVerificationEmail extends EventListener
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    /** @param UserCreated $event */
    protected function listen(Event $event):void
    {
        $this->commandBus->dispatch(
            new SendEmailVerificationEmailCommand(
                $event->email()->value()
            )
        );
    }
}
