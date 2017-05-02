<?php

namespace App;

class Response extends \Illuminate\Http\Response
{
    static function prepareErrorResponse($errors, $status = self::HTTP_INTERNAL_SERVER_ERROR)
    {
        return self::prepareResponse('error', $errors, $status);
    }

    static function prepareResponseOk($args)
    {
        return self::prepareResponse('data', $args);
    }

    static function prepareResponse($name, $args, $status = self::HTTP_OK)
    {
        return [
            $name => $args,
            'code' => $status
        ];
    }
}