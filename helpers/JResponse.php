<?php

namespace App\Helper;

use Psr\Http\Message\ResponseInterface as Response;

class JResponse
{

    static function success(Response $res, $data = null, string $msg = '', $extra = null)
    {
        $response = [];
        $response['status'] = 'success';
        $response['status_code'] = 200;
        $response['data'] = $data;
        $response['message'] = $msg;
        isset($extra) && $response['extra'] = $extra;
        // return $res->withJson($response, 200);
        $res->withStatus(200)->getBody()->write(json_encode($response));
        return $res;
    }

    static function error(Response $res, $status_code = '401', $data = [], string $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['status_code'] = $status_code;
        $response['message'] = $msg;
        $response['data'] = $data;
        // return $res->withJson($response, $status_code);
        $res->withStatus($status_code)->getBody()->write(json_encode($response));
        return $res;
    }

    static function err401(Response $res, $data = [], $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['status_code'] = 401;
        $response['message'] = $msg;
        $response['data'] = $data;

        // return $res->withJson($response, 401);
        $res->withStatus(401)->getBody()->write(json_encode($response));
        return $res;
    }
    static function err500(Response $res, $data = [], $msg = '')
    {
        $response = [];
        $response['status'] = 'error';
        $response['status_code'] = 500;
        $response['message'] = $msg;
        $response['data'] = $data;

        // return $res->withJson($response, 500);
        $res->withStatus(500)->getBody()->write(json_encode($response));
        return $res;
    }
}
