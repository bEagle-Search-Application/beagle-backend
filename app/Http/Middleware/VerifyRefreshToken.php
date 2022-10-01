<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;

class VerifyRefreshToken
{
    public function __construct(private PersonalRefreshTokenRepository $personalRefreshTokenRepository)
    {
    }

    /** @throws InvalidPersonalRefreshToken */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $this->tokenFromHeaderAsString($request->header('authorization'));

            $isAValidToken = Token::validate(
                $token,
                \env('JWT_REFRESH_SECRET')
            );
            if (!$isAValidToken) {
                throw InvalidToken::byRefreshSignature();
            }

            $userIdAsString = Token::getPayload($token)['uid'];
            $userId = UserId::fromString($userIdAsString);
            $this->personalRefreshTokenRepository->findByUserId($userId);

            return $next($request);
        } catch (InvalidToken|PersonalRefreshTokenNotFound $e) {
            return new JsonResponse(
                [
                    "response" => $e->getMessage(),
                    "status" => Response::HTTP_UNAUTHORIZED,
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    /** @throws InvalidToken */
    private function tokenFromHeaderAsString(string $header):string
    {
        try {
            return \explode(" ", $header)[1];
        } catch (\Exception $exception) {
            throw new InvalidToken($exception);
        }
    }
}
