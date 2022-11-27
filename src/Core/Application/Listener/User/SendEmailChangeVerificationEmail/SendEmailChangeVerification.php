<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Listener\User\SendEmailChangeVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailChangeVerificationEmail\SendEmailChangeVerificationEmailCommand;
use Beagle\Core\Domain\User\Event\UserEmailEdited;
use Beagle\Shared\Bus\CommandBus;
use Beagle\Shared\Bus\Event;
use Beagle\Shared\Bus\EventListener;

final class SendEmailChangeVerification extends EventListener
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    /** @param UserEmailEdited $event */
    protected function listen(Event $event):void
    {
        $this->commandBus->dispatch(
            new SendEmailChangeVerificationEmailCommand(
                $event->id()->value(),
                $event->oldEmail()->value(),
                $event->newEmail()->value()
            )
        );
    }
}
