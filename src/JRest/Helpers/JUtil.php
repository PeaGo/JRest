<?php

namespace JRest\Helpers;

use Exception;

class JUtil
{
    public static $mailer = null;

    public static $onesignal = null;

    public static $application = null;


    public static function getMailer()
    {
        if (!self::$mailer) {
            self::$mailer = self::createMailer();
        }

        $copy = clone self::$mailer;

        return $copy;
    }

    public static function getOnesignal()
    {
        if (!self::$onesignal) {
            self::$onesignal = JOneSignal::getInstance();
        }
        $copy = clone self::$onesignal;
        return $copy;
    }


    public static function getConfig($key = null)
    {

        $file = ROOT . '/config.json';
        //Get data from existing json file
        $config = file_get_contents($file);
        // converts json data into array
        $data = json_decode($config, true);
        if (!empty($key)) {
            return $data[$key];
        }
        return $data;
    }

    private static function getRandomNum()
    {

        $rndNum = rand(0, 1000);
        $rndNum = ($rndNum % 94) + 33;
        return $rndNum;
    }

    protected static function createMailer()
    {
        $conf = self::getConfig('sendmail');

        $smtpauth = ($conf['smtpauth'] == 0) ? null : 1;
        $smtpuser = $conf['smtpuser'];
        $smtppass = $conf['smtppass'];
        $smtphost = $conf['smtphost'];
        $smtpsecure = $conf['smtpsecure'];
        $smtpport = $conf['smtpport'];
        $mailfrom = $conf['mailfrom'];
        $fromname = $conf['fromname'];


        // Create a Mail object
        $mail = JMail::getInstance();

        // Clean the email address
        $mailfrom = JString::cleanLine($mailfrom);

        // Set default sender without Reply-to if the mailfrom is a valid address
        if (JString::isEmailAddress($mailfrom)) {
            // Wrap in try/catch to catch phpmailerExceptions if it is throwing them
            try {
                // Check for a false return value if exception throwing is disabled
                if ($mail->setFrom($mailfrom, JString::cleanLine($fromname), false) === false) {
                    //Log::add(__METHOD__ . '() could not set the sender data.', Log::WARNING, 'mail');
                }
            } catch (Exception $e) {
                //Log::add(__METHOD__ . '() could not set the sender data.', Log::WARNING, 'mail');
            }
        }
        $mail->useSmtp($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);

        return $mail;
    }
}
