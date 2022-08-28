<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers;

use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;

final class LogoutController extends BaseController
{
    public function execute():JsonResponse
    {
        \auth()->logout();

        return $this->generateNoContentResponse();
    }
}
