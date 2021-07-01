<?php
/**
 * PHP 7++
 *
 * LightPHPFrame
 * Copyright (c) Mo Ses
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package       App utility functions
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class App
{
    static $views;
    static $language;
    static $emails;
    static $controllers;
    static $models;
    static $vendors;

    static $get;
    static $post;
    static $file;

    static $segment;

    public static function init()
    {
        self::$get  = isset($_GET) ? $_GET : NULL;
        self::$post = isset($_POST) ? $_POST : NULL;
        self::$file = isset($_FILES) ? $_FILES : NULL;

        self::parseSegment();
    }

    public static function parseSegment()
    {
        $segment = array();

        /**
         *  Parse uri query string
         */
        if(self::$get != NULL)
        {
            $seg = explode('/',self::$get['appsegment']);
            $segments = array_filter($seg, 'strlen');
            self::$segment = $segments;
        }
    }

    public static function load()
    {
        if(class_exists('Loader'))
        {
            return new Loader();
        }
    }

    /**
     * Clean data
     *
     * @access  public
     * @return  array
     */
    public static function cleanArrayData($arr)
    {
        if(count($arr))
        {
            return filter_var_array($arr, FILTER_SANITIZE_STRING);
        } else {
            return $arr;
        }
    }

    /**
     * Do session
     *
     * @access  public
     * @return  string
     */
    public static function setSession( $k, $v )
    {
        $currentSession = array();

        if(!isset($_SESSION[SESSIONCODE])) {
            $_SESSION[SESSIONCODE] = self::arrayToString(
                array(
                    'loggedin' => false,
                    'error' => false,
                    'message' => false
                )
            );
        }

        $currentSession = self::stringToArray($_SESSION[SESSIONCODE]);

        $currentSession[$k] = $v;

        $_SESSION[SESSIONCODE] = self::arrayToString($currentSession);
    }

    /**
     * Get session
     *
     * @access  public
     * @return  string
     */
    public static function getSession($k=NULL)
    {
        $currentSession = array();

        if(!isset($_SESSION[SESSIONCODE])) {
            $_SESSION[SESSIONCODE] = self::arrayToString(
                array(
                    'loggedin' => false,
                    'error' => false,
                    'message' => false
                )
            );
        }

        $currentSession = self::stringToArray($_SESSION[SESSIONCODE]);

        if($k != NULL) {
            return isset($currentSession[$k]) ? $currentSession[$k] : false;
        } else {
            return $currentSession;
        }
    }

    public static function newOnce( $k = 'once' )
    {
        $date = date('Ymd');
        $once = self::encryptHash($k.$date);
        self::setSession( 'lpfonce', $once );
        return $once;
    }

    public static function getOnce()
    {
        return self::getSession( 'lpfonce' );
    }

    /**
     * Array to string
     *
     * @access  public
     * @return  string
     */
    public static function arrayToString($arr)
    {
        $arrs = is_array($arr) || is_object($arr) ? base64_encode(serialize($arr)) : false;
        return $arrs;
    }

    /**
     * String to array
     *
     * @access  public
     * @return  string
     */
    public static function stringToArray($str)
    {
        $strs = strlen($str)>0 ? unserialize(base64_decode($str)) : false;
        return $strs;
    }

    /**
     * Array to string
     *
     * @access  public
     * @return  string
     */
    public static function jsonEncode($arr)
    {
        $arrs = is_array($arr) || is_object($arr) ? json_encode($arr) : "";
        return $arrs;
    }

    /**
     * String to array
     *
     * @access  public
     * @return  string
     */
    public static function jsonDecode($str, $arr = true)
    {
        $strs = strlen($str) > 0 ? json_decode($str, $arr) : [];
        return $strs;
    }

    /**
     * Encrypt string mapper
     *
     * @access  public
     * @return  string
     */
    public static function encrypt($str = '')
    {
        $cryptKey  = HASHPHRASE;
        $qEncoded  = password_hash($str, PASSWORD_DEFAULT);
        return( $qEncoded );
    }

    /**
     * Decrypt string mapper
     *
     * @access  public
     * @return  string
     */
    public static function decrypt($q = '', $v = '')
    {
        $cryptKey  = HASHPHRASE;
        return password_verify( $q, $v );
    }

    /**
     * Encode hashkey
     *
     * @access private
     * @param (q)
     */
    public static function encryptHash( $q )
    {
        $rumble = '';
        $hpctr = 0;
        $inctr = 0;
        $inlen = strlen( $q );
        $hplen = strlen( HASHPHRASE );
        while( $hplen > $hpctr ){
            $rumble .= substr( HASHPHRASE, $hpctr, 1 );
            if( $hpctr > 7 && $inlen > $inctr ){
                $rumble .= substr( $q, $inctr, 1 );
                $inctr++;
            }
            $hpctr++;
        }

        return base64_encode( $rumble );
    }

    /**
     * Decode hashkey
     *
     * @access private
     * @param (q)
     */
    public static function decryptHash( $q )
    {
        $ret = '';
        $ctr = 0;
        $rumble = base64_decode( $q );
        $rulen = strlen( $rumble );
        $hplen = strlen( HASHPHRASE );
        $inlen = $rulen - $hplen;
        while( $rulen > $ctr ){
            if( $ctr > 8 && strlen( $ret ) < $inlen ){
                $ret .= substr( $rumble, $ctr, 1 );
                $ctr++;
            }
            $ctr++;
        }

        return $ret;
    }

    public static function generatePassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
                $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
                $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
                $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
                $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    public static function generateString($strength=10)
    {
        $input = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public static function uniqueId($limit = 7)
    {
        $u = md5(uniqid(microtime(true), true));
        $r = strtoupper(substr($u,0,$limit));

        return( $r );
    }

    public static function fetch($file = NULL, $load = 'ro', $data = array())
    {
        if(file_exists($file))
        {
            extract($data);

            switch(strtolower($load))
            {
                case 'require':
                case 'r':
                {
                        require($file);
                } break;
                case 'require_once':
                case 'ro':
                {
                        require_once($file);
                } break;
                case 'include':
                case 'i':
                {
                        include($file);
                }
                case 'include_once':
                case 'io':
                {
                        include_once($file);
                }
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getCurrentPage()
    {
        return implode('/',App::$segment);
    }

    public static function getFileContents($filename='dummy.txt')
    {
        $root = APPPATH;

        if(file_exists($root.$filename)) {
            return file_get_contents($root.$filename);
        } else {
            return false;
        }
    }

    public static function setFileContents($filename='dummy.txt',$html = '')
    {
        $root = APPPATH;
        return file_put_contents($root.$filename, $html);
    }

    public static function getFilesArray($folder = "")
    {
        $path = APPPATH.$folder;
        $files = array_diff(scandir($path), array('.', '..'));

        return $files;
    }

    public static function currency($int=0)
    {
        // currency
        return number_format($int,2,'.',',');
    }

    public static function date($date, $format='d/m/Y', $unix = false)
    {
        $unx = ($unix) ? $date : strtotime($date);
        return date($format,$unx);
    }

    public static function getTagContent($open='<header>',$close='</header>',$str=false,$wrap=false)
    {
        $return = '';

        if($str) {
            $one = explode($open,$str);
            $two = explode($close,$one[1]);

            $return = $two[0];
            if($wrap) {
                $return = $open.$two[0].$close;
            }
        }

        return $return;
    }

    public static function removeTagContent($open='<header>',$close='</header>',$str=false)
    {
        $return = '';

        if($str) {
            $one = explode($open,$str);
            $return .= $one[0];
            $two = explode($close,$one[1]);
            $return .= $two[1];
        }

        return $return;
    }

    public static function extractTagContent($open='<header>',$close='</header>',$str=false)
    {
        $return = array('main'=>'','fragment'=>'');

        if($str) {
            $one = explode($open,$str);
            $return['main'] .= $one[0];
            $two = explode($close,$one[1]);
            $return['main'] .= $two[1];
            $return['fragment'] = $open.$two[0].$close;
        }

        return $return;
    }

    public static function activityLog($description='', $userID = false, $userActivity = NULL )
    {
        $db = new DB();

        $user = ($userID) ? User::info(false,$userID) : User::info();

        $activity = array(
            'ActivityDescription' => $description,
            'UserID' => $user->UserID,
            'UserName' => $user->FirstName .' '. $user->LastName,
            'UserActivity' => $userActivity
        );

        $activityId = $db->insert('activity_logs',$activity);
    }

    public static function notify($UserIDs = array(), $data = array(), $SenderID = false )
    {
        $db     = new DB();
        $SentBy = ($SenderID) ? $SenderID : User::info('UserID');

        foreach ($UserIDs as $UserID) {
            $data['SenderUserID']    = $SentBy;
            $data['RecipientUserID'] = is_object($UserID) ? $UserID->UserID : $UserID;
            $notificationId          = $db->insert('site_notifications', $data);
        }
    }

    public static function readableDate($val, $sentence) {
        if ($val > 1) {
            return $val.str_replace('(s)', 's', $sentence);
        } else {
            return $val.str_replace('(s)', '', $sentence);
        }
    }

    public static function smartDate($date)
    {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;

        if ($diff <= 0) {
            return 'Now';
        }
        else if ($diff < 60) {
            return self::readableDate(floor($diff), ' second(s) ago');
        }
        else if ($diff < 60*60) {
            return self::readableDate(floor($diff/60), ' minute(s) ago');
        }
        else if ($diff < 60*60*24) {
            return self::readableDate(floor($diff/(60*60)), ' hour(s) ago');
        }
        else if ($diff < 60*60*24*30) {
            return self::readableDate(floor($diff/(60*60*24)), ' day(s) ago');
        }
        else if ($diff < 60*60*24*30*12) {
            return self::readableDate(floor($diff/(60*60*24*30)), ' month(s) ago');
        }
        else {
            return self::readableDate(floor($diff/(60*60*24*30*12)), ' year(s) ago');
        }
    }

    public static function sanitizeSlug( $text = '', $replace = '-', $nospace = false )
    {
        $text    = strtolower( $text );
        $replace = ($nospace) ? '' : $replace;
        $return  = str_replace( ' ', $replace, $text);
        $return  = preg_replace('/[^A-Za-z0-9\/\-]/', '', $return);
        return $return;
    }

    public static function addOrdinalNumberSuffix( $num )
    {
        if (!in_array(($num % 100),array(11,12,13))){
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:  return $num.'st';
                case 2:  return $num.'nd';
                case 3:  return $num.'rd';
            }
        }
        return $num.'th';
    }

    public static function setUserCookie($user)
    {
        $domain = $_SERVER['SERVER_NAME'];
        setcookie("LPFUser", $user->UserID.':'.$user->Email, time()+3600, "/", $domain, 0);

        return true;
    }

    public static function setCookie( $key='', $value='', $exp='', $path = '/' )
    {
        $domain = $_SERVER['SERVER_NAME'];
        setcookie( $key, $value, $exp, $path, $_SERVER['SERVER_NAME']);
        return true;
    }

    public static function getCookie($k)
    {
        $currentSession = false;

        if(isset($_COOKIE[$k])) {
            $currentSession = $_COOKIE[$k];
        }

        return $currentSession;
    }

    public static function normalizeString($str = '')
    {
        $str = trim($str);
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', '', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', '', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '', $str);
        $str = str_replace(' ', '_', $str);
        $str = preg_replace("/[^a-z0-9\_\-\.]/i", '', $str);
        $str = str_replace('--', '-', $str);
        return $str;
    }

    public static function getFilePathBodyClass($path)
    {
        $data = explode('views', $path);

        $filtered = str_replace(array('/', '\\'), array('/', '/'), $data[1]);
        $needle = '/';
        $newstring = $filtered;
        $pos = strpos($filtered, $needle);
        if ($pos !== false) {
            $newstring = substr_replace($filtered, '', $pos, strlen($needle));
        }
        return $newstring;
    }

    public static function mergeArgs($default = array(), $args = array())
    {
        return array_merge( (array) $default, (array) $args);
    }

    public static function acronymText($str) 
    {
        $words = explode(" ", $str);
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        return strtoupper($acronym);
    }
}

App::init();
