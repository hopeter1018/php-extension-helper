<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\ExtensionHelper;

/**
 * Description of SwiftMailer
 *
 * @version $id$
 * @author peter.ho
 */
class SwiftMailer
{

// <editor-fold defaultstate="collapsed" desc="Transport configs">

    public static $HOST = '127.0.0.1';
    public static $PORT = 25;
    /**
     * in seconds.
     * @var int
     */
    public static $TIMEOUT = 30;
    public static $USEAUTH = false;
    public static $USERNAME = null;
    public static $PASSWORD = null;

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Message configs">

    public static $CONTENTTYPE = 'text/html';
    public static $CHARSET = 'utf-8';

// </editor-fold>

    /**
     * 
     * @return Swift_SmtpTransport
     */
    private static function getTransport()
    {
        $transport = \Swift_SmtpTransport::newInstance(self::$HOST)
            ->setPort(self::$PORT)
            ->setTimeout(self::$TIMEOUT);
        if (self::$USEAUTH === true) {
            if (is_string(self::$USERNAME) and is_string(self::$PASSWORD) and trim(self::$USERNAME) != '') {
                $transport->setUsername(self::$USERNAME)->setPassword(self::$PASSWORD);
            } else {
                die('Please specify the username and password or set USEAUTH to false');
            }
        }
        return $transport;
    }

    /**
     * 
     * @return \Swift_Mailer
     */
    private static function newMailer()
    {
        return new \Swift_Mailer(self::getTransport());
    }

    /**
     * 
     * @param string $subject
     * @param string $body
     * @return \Swift_Message
     */
    public static function newMessage($subject, $body)
    {
        return \Swift_Message::newInstance($subject, $body, self::$CONTENTTYPE, self::$CHARSET);
    }

    /**
     * 
     * @param \Swift_Message $message
     * @return boolean
     */
    public static function send(\Swift_Message $message)
    {
        try {
            static::newMailer()->send($message);
            $success = true;
        } catch (\Exception $ex) {
            $success = false;
        }
        return $success;
    }

}
