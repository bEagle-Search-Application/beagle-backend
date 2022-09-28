<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email;

interface EmailSender
{
    public function send(array $emailsToSend, ComposedEmail $composedMail):void;
}
