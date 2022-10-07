<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Beagle\Shared\Bus\CommandBus;
use Beagle\Shared\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    public function __construct(
        protected CommandBus $commandBus,
        protected QueryBus $queryBus
    ) {
    }

    protected function generateNotFoundResponse(string $response):JsonResponse
    {
        return $this->generateJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    protected function generateBadRequestResponse(string $response):JsonResponse
    {
        return $this->generateJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }

    protected function generateUnauthorizedResponse(string $response):JsonResponse
    {
        return $this->generateJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    protected function generateSuccessfulResponse(array $response):JsonResponse
    {
        return new JsonResponse(
            [
                "response" => $response,
                "status" => Response::HTTP_OK,
            ],
            Response::HTTP_OK
        );
    }

    protected function generateNoContentResponse():JsonResponse
    {
        return new JsonResponse(
            status: Response::HTTP_NO_CONTENT
        );
    }

    private function generateJsonResponse(string $response, int $code):JsonResponse
    {
        return new JsonResponse(
            [
                "response" => $response,
                "status" => $code,
            ],
            $code
        );
    }
}
