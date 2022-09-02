<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Beagle\Shared\Infrastructure\Http\Api\HttpErrorCode;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    protected function generateNotFoundResponse(string $response):JsonResponse
    {
        return $this->generateJsonResponse($response, HttpErrorCode::NOT_FOUND);
    }

    protected function generateBadRequestResponse(string $response):JsonResponse
    {
        return $this->generateJsonResponse($response, HttpErrorCode::BAD_REQUEST);
    }

    protected function generateSuccessfulResponse(array $response):JsonResponse
    {
        return new JsonResponse(
            [
                "response" => $response,
                "status" => HttpErrorCode::OK,
            ],
            HttpErrorCode::OK
        );
    }

    protected function generateNoContentResponse():JsonResponse
    {
        return new JsonResponse(
            status: HttpErrorCode::NO_CONTENT
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
