<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
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
            $token = $this->tokenFromHeaderAsString($request->header('authorization'));

            $this->validateSignatureOf($token);
            $this->validateExpirationOf($token);
            $this->validateUserOf($token);

            return $next($request);
        } catch (InvalidToken|PersonalAccessTokenNotFound|TokenExpired $e) {
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
    private function validateSignatureOf(string $token):void
    {
        $isAValidToken = Token::validate(
            $token,
            \env('JWT_ACCESS_SECRET')
        );
        if (!$isAValidToken) {
            throw InvalidToken::byAccessSignature();
        }
    }

    /** @throws TokenExpired */
    private function validateExpirationOf(string $token):void
    {
        $isANonExpirationToken = Token::validateExpiration($token);
        if (!$isANonExpirationToken) {
            throw TokenExpired::byExpirationDate();
        }
    }

    /**
     * @throws PersonalAccessTokenNotFound
     * @throws InvalidPersonalAccessToken
     */
    private function validateUserOf(string $token):void
    {
        $userIdAsString = Token::getPayload($token)['uid'];
        $userId = UserId::fromString($userIdAsString);
        $this->personalAccessTokenRepository->findByUserId($userId);
    }
}
