<?php

namespace App\Http\Middleware;

use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAuthToken
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $token = UserToken::fromString($request->header('authorization'));
            $this->userRepository->findByToken($token);

            return $next($request);
        } catch (UserNotFound $e) {
            return new JsonResponse(
                [
                    "message" => $e->getMessage(),
                    "status" => Response::HTTP_FORBIDDEN,
                ],
                Response::HTTP_FORBIDDEN
            );
        }
    }
}
