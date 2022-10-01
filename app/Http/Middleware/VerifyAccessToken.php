<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessToken
{
    public function __construct(private PersonalAccessTokenRepository $personalAccessTokenRepository)
    {
    }

    /** @throws InvalidPersonalAccessToken */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->header('authorization');

            $isAValidToken = Token::validate(
                $request->header('authorization'),
                \env('JWT_ACCESS_SECRET')
            );
            if (!$isAValidToken) {
                throw InvalidToken::byAccessSignature();
            }

            $userIdAsString = Token::getPayload($token)['uid'];
            $userId = UserId::fromString($userIdAsString);
            $this->personalAccessTokenRepository->findByUserId($userId);

            return $next($request);
        } catch (InvalidToken|PersonalAccessTokenNotFound $e) {
            return new JsonResponse(
                [
                    "response" => $e->getMessage(),
                    "status" => Response::HTTP_UNAUTHORIZED,
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
