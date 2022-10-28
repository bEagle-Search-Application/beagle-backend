<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;

final class PersonalAccessToken extends PersonalToken
{
    /** @throws InvalidPersonalAccessToken */
    public function __construct(
        PersonalTokenId $id,
        UserId $userId,
        Token $token
    ) {
        if (!$token->isAnAccessToken()) {
            throw InvalidPersonalAccessToken::byType();
        }

        parent::__construct($id, $userId, $token);
    }
}
