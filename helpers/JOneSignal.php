<?php

namespace App\Helper;
use App\Helper\Log;

// onesignal 
// defined('ONESIGNAL_API_KEY', 'YjNmMDk4YzktOTZjMC00ZTM1LTllN2UtNTgzODYyNzIwMGE2');
// defined('ONESIGNAL_APP_ID', '8f246b62-0c90-44b7-88b9-d0f7df438a2e');
class JOneSignal
{

    /**
     * $data [ 
     *      'title' => '', exp : New user
     *      'body' => '', exp : Peago has been created account by facebook
     *      'data' => object 
     *  ] 
     */

    private static function notify(
        $playerids = [],
        $content = 'Notify from GoPanda',
        $data = array()
    ) {
        $fields = array(
            'app_id' => ONESIGNAL_APP_ID,
            'include_player_ids' => $playerids,
            'data' => $data,
            'contents' => array(
                "en" => $content['body']
            ),
            'headings' => array(
                "en" => $content['title']
            ),
        );
        var_dump($playerids);
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YjNmMDk4YzktOTZjMC00ZTM1LTllN2UtNTgzODYyNzIwMGE2'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        try {
            $response = curl_exec($ch);
            var_dump($response);
        } catch (\Throwable $th) {
            // throw $th;
        }
        curl_close($ch);
        return json_decode($response);
    }
    public static function push($target, $content = null, $data = null)
    {
        $receipents = JNotification::loadTargetPush($target);
        try {
            // var_dump($receipents);
            self::notify($receipents, $content, $data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [__FILE__, __LINE__]);
        }
    }
}
