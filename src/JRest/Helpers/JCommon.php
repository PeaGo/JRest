<?php

use App\Model\Order;

function getUserAgent()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $detect = new Mobile_Detect();
    $detect->setUserAgent($user_agent);
    $info = $detect->isiOS() ? 'ios' : ($detect->isAndroidOS() ? 'android' : 'desktop');
    return $info;
}
// remove duplicate item array with field
function unique_multidim_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[] = $val;
        }
        $i++;
    }
    return $temp_array;
}

// generate order number

function generate_order_number()
{
    return date("ymdHis");
}
// GET config.json
function JGetConfig($key = null)
{
    $file = APP_PATH . '/config.json';
    //Get data from existing json file
    $config = file_get_contents($file);
    // converts json data into array
    $data = json_decode($config, true);
    if (!empty($key)) {
        return $data[$key];
    }
    return $data;
}
