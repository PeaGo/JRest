<?php

namespace JRest\Helpers;

class JString
{

    public static function stringURLSafe($string, $language = '')
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $string);

        // Trim white spaces at beginning and end of alias and make lowercase
        $str = trim(strtolower($str));

        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');

        return $str;
    }
    public static function stringNameSafe($string, $language = '')
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('_', ' ', $string);

        // Trim white spaces at beginning and end of alias and make lowercase
        $str = trim(strtolower($str));

        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');

        return $str;
    }
    public static function vn_to_str($str)
    {
        $unicode = array(

            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

            'd' => 'đ',

            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

            'i' => 'í|ì|ỉ|ĩ|ị',

            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',

            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

            'D' => 'Đ',

            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',

            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

        );

        foreach ($unicode as $nonUnicode => $uni) {

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(' ', '_', $str);

        return $str;
    }
    public static function cleanLine($value)
    {
        // $value = \JStringPunycode::emailToPunycode($value);

        return trim(preg_replace('/(%0A|%0D|\n+|\r+)/i', '', $value));
    }
    public static function cleanText($value)
    {
        return trim(preg_replace('/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i', '', $value));
    }
    public static function cleanBody($body)
    {
        // Strip all email headers from a string
        return preg_replace("/((From:|To:|Cc:|Bcc:|Subject:|Content-type:) ([\S]+))/", '', $body);
    }
    public static function cleanSubject($subject)
    {
        return preg_replace("/((From:|To:|Cc:|Bcc:|Content-type:) ([\S]+))/", '', $subject);
    }

    /**
     * Verifies that an email address does not have any extra headers injected into it.
     *
     * @param   string  $address  email address.
     *
     * @return  mixed   email address string or boolean false if injected headers are present.
     *
     * @since   1.7.0
     */
    public static function cleanAddress($address)
    {
        if (preg_match("[\s;,]", $address)) {
            return false;
        }

        return $address;
    }
    public static function isEmailAddress($email)
    {
        // Split the email into a local and domain
        $atIndex = strrpos($email, '@');
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);

        // Check Length of domain
        $domainLen = strlen($domain);

        if ($domainLen < 1 || $domainLen > 255) {
            return false;
        }

        /*
         * Check the local address
         * We're a bit more conservative about what constitutes a "legal" address, that is, a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-
         * The first and last character in local cannot be a period ('.')
         * Also, period should not appear 2 or more times consecutively
         */
        $allowed = "a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-";
        $regex = "/^[$allowed][\.$allowed]{0,63}$/";

        if (!preg_match($regex, $local) || substr($local, -1) == '.' || $local[0] == '.' || preg_match('/\.\./', $local)) {
            return false;
        }

        // No problem if the domain looks like an IP address, ish
        $regex = '/^[0-9\.]+$/';

        if (preg_match($regex, $domain)) {
            return true;
        }

        // Check Lengths
        $localLen = strlen($local);

        if ($localLen < 1 || $localLen > 64) {
            return false;
        }

        // Check the domain
        $domain_array = explode('.', $domain);
        $regex = '/^[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';

        foreach ($domain_array as $domain) {
            // Convert domain to punycode
            // $domain = \JStringPunycode::toPunycode($domain);

            // Must be something
            if (!$domain) {
                return false;
            }

            // Check for invalid characters
            if (!preg_match($regex, $domain)) {
                return false;
            }

            // Check for a dash at the beginning of the domain
            if (strpos($domain, '-') === 0) {
                return false;
            }

            // Check for a dash at the end of the domain
            $length = strlen($domain) - 1;

            if (strpos($domain, '-', $length) === $length) {
                return false;
            }
        }

        return true;
    }
    public static function substr($str, $offset, $length = false)
    {
        if ($length === false) {
            return substr($str, $offset);
        }

        return substr($str, $offset, $length);
    }

    public static function strlen($str)
    {
        return strlen($str);
    }
}
