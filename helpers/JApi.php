<?php

use Psr\Http\Message\ResponseInterface as Response;

define('STATUS_SUCCESS', 'success');
define('STATUS_ERROR', 'error');
function setResponse(string $status, int $status_code, string $msg, $data = null, Response $res)
{
    $response = [];
    if ($status == STATUS_ERROR) {
        $response['status'] = 'error';
        $response['status_code'] = $status_code;
        $response['msg'] = $msg;
        $response['data'] = $data;
    } else if ($status == STATUS_SUCCESS) {
        $response['status'] = 'success';
        $response['status_code'] = $status_code;
        $response['msg'] = $msg;
        $response['data'] = $data;
    }
    // return $res->getBo($response, $status_code);
    $res->withStatus($status_code)->getBody()->write(json_encode($response));
    return $res;
}

function isValidParams($params, $arr)
{
    $res = [
        'status' => false,
        'msg' => ''
    ];
    foreach ($arr as $key => $value) {
        if (isset($params[$value]) && $params[$value] === 0) {
            continue;
        }
        if (empty($params[$value])) {
            $res['status'] = false;
            $res['msg'] = $value;
            return $res;
        }
    }
    $res['status'] = true;
    return $res;
}
