<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Email\ComposedEmail;
use Beagle\Core\Infrastructure\Email\EmailSender;
use Beagle\Shared\Domain\ValueObjects\Token;

final class LaravelUserVerificationEmailSender implements UserVerificationEmailSender
{
    private const DEFAULT_EMAIL_SUBJECT = "Verificación de email";
    private const SPA_PATH_TO_VERIFY = "/users/%s/email-confirmation";

    public function __construct(private EmailSender $emailSender)
    {
    }

    public function execute(Token $token, User $user):void
    {
        $composedEmail = new ComposedEmail(
            self::DEFAULT_EMAIL_SUBJECT,
            view(
                'email.verification',
                [
                    "urlToAcceptVerification" => \env('SPA_URL')
                                                 . \sprintf(
                                                     self::SPA_PATH_TO_VERIFY,
                                                     $user->id()->value()
                                                 )
                                                 . "?token=" . $token->value()
                ]
            )->render()
        );

        $this->emailSender->send([$user->email()->value()], $composedEmail);
    }
}
