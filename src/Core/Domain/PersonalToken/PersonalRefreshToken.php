<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;

final class PersonalRefreshToken extends PersonalToken
{
    /** @throws InvalidPersonalRefreshToken */
    public function __construct(
        PersonalTokenId $id,
        UserId $userId,
        Token $token,
    ) {
        if (!$token->isARefreshToken()) {
            throw InvalidPersonalRefreshToken::byType();
        }

        parent::__construct($id, $userId, $token);
    }
}
