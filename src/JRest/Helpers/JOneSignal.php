<?php

namespace JRest\Helpers;

// onesignal 
// defined('ONESIGNAL_API_KEY', 'YjNmMDk4YzktOTZjMC00ZTM1LTllN2UtNTgzODYyNzIwMGE2');
// defined('ONESIGNAL_APP_ID', '8f246b62-0c90-44b7-88b9-d0f7df438a2e');
class JOneSignal
{
    protected static $api_key = '';
    protected static $app_id = '';
    protected static $instance;
    public function __construct()
    {
        $onesignal_conf = JUtil::getConfig('onesignal');
        self::$api_key = $onesignal_conf['ONESIGNAL_API_KEY'];
        self::$app_id = $onesignal_conf['ONESIGNAL_APP_ID'];
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new JOneSignal();
        }
        return self::$instance;
    }
    public static function notify(
        $playerids = [],
        $content = [],
        $data = []
    ) {
        // var_dump($data);return;
        $fields = array(
            'app_id' => self::$app_id,
            'include_player_ids' => $playerids,
            $data && 'data' => $data,
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic ' . self::$api_key));
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
}
