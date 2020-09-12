<?php

namespace App\Helper;

require __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{

    public static function info($msg, $context = [])
    {

        $logger = new Logger('Syslog');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/info.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());
        $logger->info($msg, $context);
    }
    public static function error($msg, $context = [])
    {
        $log = new Logger('Syslog');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../logs/error.log', Logger::DEBUG));
        $log->pushHandler(new FirePHPHandler());
        $log->error($msg, $context);
    }
}
