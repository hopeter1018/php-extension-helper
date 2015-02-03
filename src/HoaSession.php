<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\ExtensionHelper;

use Hoa\Session\Session;

/**
 * Description of HoaSession
 *
 * @version $id$
 * @author peter.ho
 */
class HoaSession
{

    /**
     * Get the named session segment<br />
     * If $name is not present, return global segment
     * 
     * @param string|null $name 
     * @return Session
     */
    public static function segment($name = null)
    {
        $nameReal = $name ?: '';
        return new Session($nameReal);
    }

    /**
     * Get the named, suffix by BRWOSER_KEY, session segment
     * 
     * @param string|null $name
     * @return Session
     */
    public static function browserSegment($name = null)
    {
        $nameReal = $name ?: '';
        return new Session($nameReal . $_SERVER['HTTP_BROWSER_KEY']);
    }

    /**
     * Get a CSRF token
     * @return string
     */
    public static function getCsrf()
    {
        $session = new Session('csrf');
        return $session['value'] = \Hopeter1018\Helper\String::randomString();
    }

    /**
     * Validate the posted CSRF token
     * 
     * @param string $value
     * @return boolean
     */
    public static function validateCsrf($value)
    {
        $session = new Session('csrf');
        return $session['value'] === $value;
    }

}
