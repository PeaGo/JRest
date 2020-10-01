<?php

namespace JRest\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class JMail  extends PHPMailer
{
    protected static $instances = array();

    /**
     * Charset of the message.
     *
     * @var    string
     * @since  1.7.0
     */
    public $CharSet = 'utf-8';

    /**
     * Constructor
     *
     * @param   boolean  $exceptions  Flag if Exceptions should be thrown
     *
     * @since   1.7.0
     */
    public function __construct($exceptions = true)
    {
        parent::__construct($exceptions);



        // Configure a callback function to handle errors when $this->edebug() is called
        $this->Debugoutput = function ($message, $level) {
        };

        // If debug mode is enabled then set SMTPDebug to the maximum level
        if (defined('DEBUG_MODE') && DEBUG_MODE == 'true') {
            $this->SMTPDebug = 4;
        }

        // Don't disclose the PHPMailer version
        $this->XMailer = ' ';
    }

    /**
     * Returns the global email object, only creating it if it doesn't already exist.
     *
     * NOTE: If you need an instance to use that does not have the global configuration
     * values, use an id string that is not 'JTravel'.
     *
     * @param   string   $id          The id string for the Mail instance [optional]
     * @param   boolean  $exceptions  Flag if Exceptions should be thrown [optional]
     *
     * @return  JMail The global Mail object
     *
     * @since   1.7.0
     */
    public static function getInstance($id = 'JTravel', $exceptions = true)
    {
        if (empty(self::$instances[$id])) {
            self::$instances[$id] = new JMail($exceptions);
        }

        return self::$instances[$id];
    }


    public function Send()
    {
        try {
            // Try sending with default settings
            $result = parent::send();
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * Set the From and FromName properties.
     *
     * @param   string   $address  The sender email address
     * @param   string   $name     The sender name
     * @param   boolean  $auto     Whether to also set the Sender address, defaults to true
     *
     * @return  boolean
     *
     * @since   1.7.0
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        try {
            if (parent::setFrom($address, $name, $auto) === false) {
                return false;
            }
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * Set the email sender
     *
     * @param   mixed  $from  email address and Name of sender
     *                        <code>array([0] => email Address, [1] => Name)</code>
     *                        or as a string
     *
     * @return  JMail|boolean  Returns this object for chaining on success or boolean false on failure.
     *
     * @since   1.7.0
     * @throws  \UnexpectedValueException
     */
    public function setSender($from)
    {
        // Wrapped in try/catch if PHPMailer is configured to throw exceptions
        try {
            if (is_array($from)) {
                // If $from is an array we assume it has an address and a name
                if (isset($from[2])) {
                    // If it is an array with entries, use them
                    $result = $this->setFrom(JString::cleanLine($from[0]), JString::cleanLine($from[1]), (bool) $from[2]);
                } else {
                    $result = $this->setFrom(JString::cleanLine($from[0]), JString::cleanLine($from[1]));
                }
            } elseif (is_string($from)) {
                // If it is a string we assume it is just the address
                $result = $this->setFrom(JString::cleanLine($from));
            } else {
                return false;
            }

            // Check for boolean false return if exception handling is disabled
            if ($result === false) {
                return false;
            }
        } catch (Exception $e) {

            return false;
        }

        return $this;
    }

    /**
     * Set the email subject
     *
     * @param   string  $subject  Subject of the email
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   1.7.0
     */
    public function setSubject($subject)
    {
        $this->Subject = JString::cleanLine($subject);

        return $this;
    }

    /**
     * Set the email body
     *
     * @param   string  $content  Body of the email
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   1.7.0
     */
    public function setBody($content)
    {
        /*
         * Filter the Body
         * TODO: Check for XSS
         */
        $this->Body = JString::cleanText($content);

        return $this;
    }

    /**
     * Add recipients to the email.
     *
     * @param   mixed   $recipient  Either a string or array of strings [email address(es)]
     * @param   mixed   $name       Either a string or array of strings [name(s)]
     * @param   string  $method     The parent method's name.
     *

     */
    protected function add($recipient, $name = [''], $method = 'addAddress')
    {
        $method = lcfirst($method);

        // If the recipient is an array, add each recipient... otherwise just add the one
        if (is_array($recipient)) {
            if (is_array($name)) {
                $combined = array_combine($recipient, $name);

                if ($combined === false) {
                    return false;
                }

                foreach ($combined as $recipientEmail => $recipientName) {
                    $recipientEmail = JString::cleanLine($recipientEmail);
                    $recipientName = JString::cleanLine($recipientName);

                    // Wrapped in try/catch if PHPMailer is configured to throw exceptions
                    try {
                        // Check for boolean false return if exception handling is disabled
                        if (call_user_func('parent::' . $method, $recipientEmail, $recipientName) === false) {
                            return false;
                        }
                    } catch (Exception $e) {


                        return false;
                    }
                }
            } else {
                $name = JString::cleanLine($name);

                foreach ($recipient as $to) {
                    $to = JString::cleanLine($to);

                    // Wrapped in try/catch if PHPMailer is configured to throw exceptions
                    try {
                        // Check for boolean false return if exception handling is disabled
                        if (call_user_func('parent::' . $method, $to, $name) === false) {
                            return false;
                        }
                    } catch (Exception $e) {

                        return false;
                    }
                }
            }
        } else {
            $recipient = JString::cleanLine($recipient);

            // Wrapped in try/catch if PHPMailer is configured to throw exceptions
            try {
                // Check for boolean false return if exception handling is disabled
                if (call_user_func('parent::' . $method, $recipient, $name) === false) {
                    return false;
                }
            } catch (Exception $e) {

                return false;
            }
        }

        return $this;
    }

    /**
     * Add recipients to the email
     *
     * @param   mixed  $recipient  Either a string or array of strings [email address(es)]
     * @param   mixed  $name       Either a string or array of strings [name(s)]
     *
     * @return  JMail|boolean  Returns this object for chaining.
     *
     * @since   1.7.0
     */
    public function addRecipient($recipient, $name = '')
    {
        return $this->add($recipient, $name, 'addAddress');
    }

    /**
     * Add carbon copy recipients to the email
     *
     * @param   mixed  $cc    Either a string or array of strings [email address(es)]
     * @param   mixed  $name  Either a string or array of strings [name(s)]
     *
     * @return  JMail|boolean  Returns this object for chaining on success or boolean false on failure.
     *
     * @since   1.7.0
     */
    public function addCc($cc, $name = '')
    {
        // If the carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset($cc)) {
            return $this->add($cc, $name, 'addCC');
        }

        return $this;
    }

    /**
     * Add blind carbon copy recipients to the email
     *
     * @param   mixed  $bcc   Either a string or array of strings [email address(es)]
     * @param   mixed  $name  Either a string or array of strings [name(s)]
     *
     * @return  JMail|boolean  Returns this object for chaining on success or boolean false on failure.
     *
     * @since   1.7.0
     */
    public function addBcc($bcc, $name = '')
    {
        // If the blind carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset($bcc)) {
            return $this->add($bcc, $name, 'addBCC');
        }

        return $this;
    }

    /**
     * Add file attachment to the email
     *
     * @param   mixed   $path         Either a string or array of strings [filenames]
     * @param   mixed   $name         Either a string or array of strings [names]. N.B. if this is an array it must contain the same
     *                                number of elements as the array of paths supplied.
     * @param   mixed   $encoding     The encoding of the attachment
     * @param   mixed   $type         The mime type
     * @param   string  $disposition  The disposition of the attachment
     *
     * @return  JMail|boolean  Returns this object for chaining on success or boolean false on failure.
     *
     * @since   3.0.1
     * @throws  \InvalidArgumentException
     */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $disposition = 'attachment')
    {
        // If the file attachments is an array, add each file... otherwise just add the one
        if (isset($path)) {
            // Wrapped in try/catch if PHPMailer is configured to throw exceptions
            try {
                $result = true;

                if (is_array($path)) {
                    if (!empty($name) && count($path) != count($name)) {
                        //throw new('The number of attachments must be equal with the number of name');
                    }

                    foreach ($path as $key => $file) {
                        if (!empty($name)) {
                            $result = parent::addAttachment($file, $name[$key], $encoding, $type, $disposition);
                        } else {
                            $result = parent::addAttachment($file, $name, $encoding, $type, $disposition);
                        }
                    }
                } else {
                    $result = parent::addAttachment($path, $name, $encoding, $type, $disposition);
                }

                // Check for boolean false return if exception handling is disabled
                if ($result === false) {
                    return false;
                }
            } catch (Exception $e) {

                return false;
            }
        }

        return $this;
    }

    /**
     * Unset all file attachments from the email
     *
     *
     * @since   3.0.1
     */
    public function clearAttachments()
    {
        parent::clearAttachments();

        return $this;
    }

    /**
     * Unset file attachments specified by array index.
     *
     * @param   integer  $index  The numerical index of the attachment to remove
     *
     *
     * @since   3.0.1
     */
    public function removeAttachment($index = 0)
    {
        if (isset($this->attachment[$index])) {
            unset($this->attachment[$index]);
        }

        return $this;
    }

    /**
     * Add Reply to email address(es) to the email
     *
     * @param   mixed  $replyto  Either a string or array of strings [email address(es)]
     * @param   mixed  $name     Either a string or array of strings [name(s)]
     *
     * @return  JMail |boolean  Returns this object for chaining on success or boolean false on failure.
     *
     * @since   1.7.0
     */
    public function addReplyTo($replyto, $name = '')
    {
        return $this->add($replyto, $name, 'addReplyTo');
    }






    public function useSmtp($auth = null, $host = null, $user = null, $pass = null, $secure = null, $port = 25)
    {
        $this->SMTPAuth = $auth;
        $this->Host = $host;
        $this->Username = $user;
        $this->Password = $pass;
        $this->Port = $port;

        if ($secure == 'ssl' || $secure == 'tls') {
            $this->SMTPSecure = $secure;
        }

        if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null)
            || ($this->SMTPAuth === null && $this->Host !== null)
        ) {
            $this->isSMTP();

            return true;
        } else {
            $this->isMail();

            return false;
        }
    }

    /**
     * Function to send an email
     *
     * @param   string   $from         From email address
     * @param   string   $fromName     From name
     * @param   mixed    $recipient    Recipient email address(es)
     * @param   string   $subject      email subject
     * @param   string   $body         Message body
     * @param   boolean  $mode         false = plain text, true = HTML
     * @param   mixed    $cc           CC email address(es)
     * @param   mixed    $bcc          BCC email address(es)
     * @param   mixed    $attachment   Attachment file name(s)
     * @param   mixed    $replyTo      Reply to email address(es)
     * @param   mixed    $replyToName  Reply to name(s)
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function sendMail(
        $from,
        $fromName,
        $recipient,
        $subject,
        $body,
        $mode = false,
        $cc = null,
        $bcc = null,
        $attachment = null,
        $replyTo = null,
        $replyToName = null
    ) {

        $this->setSubject($subject);
        $this->setBody($body);
        $this->isHtml($mode);

        /*
         * Do not send the message if adding any of the below items fails
         */

        if ($this->addRecipient($recipient) === false) {
            return false;
        }

        if ($this->addCc($cc) === false) {
            return false;
        }

        if ($this->addBcc($bcc) === false) {
            return false;
        }

        if ($this->addAttachment($attachment) === false) {
            return false;
        }

        // Take care of reply email addresses
        if (is_array($replyTo)) {
            $numReplyTo = count($replyTo);

            for ($i = 0; $i < $numReplyTo; $i++) {
                if ($this->addReplyTo($replyTo[$i], $replyToName[$i]) === false) {
                    return false;
                }
            }
        } elseif (isset($replyTo)) {
            if ($this->addReplyTo($replyTo, $replyToName) === false) {
                return false;
            }
        }


        // Add sender to replyTo only if no replyTo received
        $autoReplyTo = (empty($this->ReplyTo)) ? true : false;

        if ($this->setSender(array($from, $fromName, $autoReplyTo)) === false) {
            return false;
        }

        return $this->Send();
    }
    public function isHtml($ishtml = true)
    {
        parent::isHTML($ishtml);

        return $this;
    }
}
