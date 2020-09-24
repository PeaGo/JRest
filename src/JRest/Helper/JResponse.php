<?php

namespace JRest\Helper;

use Slim\Http\Response;

class JResponse
{

    static function success(Response $res, $data = null, string $msg = '', $extra = null)
    {
        $response = [];
        $response['status'] = 'success';
        $response['code'] = 200;
        $response['data'] = $data;
        $response['message'] = $msg;
        isset($extra) && $response['extra'] = $extra;
        return $res->withJson($response, 200);
    }

    static function error(Response $res, $status_code = '401', $data = [], string $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['code'] = $status_code;
        $response['message'] = $msg;
        $response['data'] = $data;
        return $res->withJson($response, $status_code);
       
    }

    static function err401(Response $res, $data = [], $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['code'] = 401;
        $response['message'] = $msg;
        $response['data'] = $data;
        return $res->withJson($response, 401);
        
    }
    static function err500(Response $res, $data = [], $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['code'] = 500;
        $response['message'] = $msg;
        $response['data'] = $data;
        return $res->withJson($response, 500);
    }
}
