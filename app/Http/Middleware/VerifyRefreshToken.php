<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\PayloadValueNotFound;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Http\Errors\HeaderNotFound;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $token = $this->tokenFromHeaderAsString($this->header($request, 'authorization'));

            $this->validateToken(Token::refreshTokenFromString($token));

            return $next($request);
        } catch (InvalidTokenSignature|TokenExpired|HeaderNotFound $unauthorizedException) {
            return new JsonResponse(
                [
                    "response" => $unauthorizedException->getMessage(),
                    "status" => Response::HTTP_UNAUTHORIZED,
                ],
                Response::HTTP_UNAUTHORIZED
            );
        } catch (PersonalRefreshTokenNotFound $forbiddenException) {
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

    /**
     * @throws InvalidTokenSignature
     * @throws PersonalRefreshTokenNotFound
     * @throws TokenExpired
     * @throws InvalidPersonalRefreshToken
     * @throws PayloadValueNotFound
     */
    public function validateToken(Token $token):void
    {
        $userIdAsString = $token->getValueFromPayloadByKey('uid');
        $userId = UserId::fromString($userIdAsString);
        $this->personalRefreshTokenRepository->findByUserIdAndToken($userId, $token);
    }

    /** @throws HeaderNotFound */
    private function header(Request $request, string $key):array|null|string
    {
        $header = $request->header($key);

        if (empty($header)) {
            throw HeaderNotFound::byValue($key);
        }

        return $header;
    }
}
