<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenType;
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
        } catch (InvalidTokenSignature|TokenExpired $unauthorizedException) {
            return new JsonResponse(
                [
                    "response" => $unauthorizedException->getMessage(),
                    "status" => Response::HTTP_UNAUTHORIZED,
                ],
                Response::HTTP_UNAUTHORIZED
            );
        } catch (PersonalAccessTokenNotFound $forbiddenException) {
            return new JsonResponse(
                [
                    "response" => $forbiddenException->getMessage(),
                    "status" => Response::HTTP_FORBIDDEN,
                ],
                Response::HTTP_FORBIDDEN
            );
        }
    }

    /** @throws InvalidTokenSignature */
    private function tokenFromHeaderAsString(string $header):string
    {
        try {
            return \explode(" ", $header)[1];
        } catch (\Exception $exception) {
            throw new InvalidTokenSignature($exception->getMessage());
        }
    }

    /** @throws InvalidTokenSignature */
    private function validateSignatureOf(string $token):void
    {
        $isAValidToken = Token::validate(
            $token,
            \env('JWT_ACCESS_SECRET')
        );
        if (!$isAValidToken) {
            throw InvalidTokenSignature::byType(TokenType::ACCESS);
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
