<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function validationError($errors = [])
    {
        return $this->sendJsonErrors($errors, Response::HTTP_BAD_REQUEST);
    }

    protected function sendJsonErrors($errors, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->sendJsonResponse(Response::prepareErrorResponse($errors, $status));
    }


    protected function sendJson($data)
    {
        return $this->sendJsonResponse(Response::prepareResponseOk($data));
    }


    private function sendJsonResponse($data)
    {
        return response()->json($data);
    }
}
