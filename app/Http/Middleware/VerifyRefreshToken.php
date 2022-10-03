<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
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

            $this->validateSignatureOf($token);
            $this->validateExpirationOf($token);
            $this->validateUserOf($token);

            return $next($request);
        } catch (InvalidToken|PersonalRefreshTokenNotFound|TokenExpired $e) {
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
            throw new InvalidToken($exception->getMessage());
        }
    }

    /** @throws InvalidToken */
    public function validateSignatureOf(string $token):void
    {
        $isAValidToken = Token::validate(
            $token,
            \env('JWT_REFRESH_SECRET')
        );
        if (!$isAValidToken) {
            throw InvalidToken::byRefreshSignature();
        }
    }

    /** @throws TokenExpired */
    public function validateExpirationOf(string $token):void
    {
        $isANonExpirationToken = Token::validateExpiration($token);
        if (!$isANonExpirationToken) {
            throw TokenExpired::byExpirationDate();
        }
    }

    /**
     * @throws PersonalRefreshTokenNotFound
     * @throws InvalidPersonalRefreshToken
     */
    public function validateUserOf(string $token):void
    {
        $userIdAsString = Token::getPayload($token)['uid'];
        $userId = UserId::fromString($userIdAsString);
        $this->personalRefreshTokenRepository->findByUserId($userId);
    }
}
