<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email;

use Illuminate\Support\Facades\Mail;

final class LaravelEmailSender implements EmailSender
{
    public function __construct(private string $fromEmail, private string $fromName)
    {
    }

    public function send(array $emailsToSend, ComposedEmail $composedMail):void
    {
        Mail::html(
            $composedMail->content(),
            function ($message) use ($emailsToSend, $composedMail) {
                $message
                    ->to($emailsToSend)
                    ->from($this->fromEmail, $this->fromName)
                    ->subject($composedMail->subject());
            }
        );
    }
}
