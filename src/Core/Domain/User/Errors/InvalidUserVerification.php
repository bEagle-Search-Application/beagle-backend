<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Email;

final class InvalidUserVerification extends \Exception
{
    private const VERIFICATION_EXPIRED = "La verificación de usuario ha expirado el %s";
    private const INVALID_EMAIL = "El email %s no corresponde con la solicitud de verificación";

    public static function byExpiredAt(DateTime $expiredAt):self
    {
        return new self(\sprintf(self::VERIFICATION_EXPIRED, $expiredAt->jsonSerialize()));
    }

    public static function byEmail(Email $email):self
    {
        return new self(\sprintf(self::INVALID_EMAIL, $email->value()));
    }
}
