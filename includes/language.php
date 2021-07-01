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
 * @package       Core language
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Lang
{    
    static $package;
    static $language;
    
    static $rootfile;
    static $now;
    static $langs;

    const ext = ".txt";
    
    public static function init() {
        $db = new DB();
        self::$langs = $db->from('languages')->all(false, false, 'LanguageCode');

        self::current();
        self::fileSourceInit();
        self::dbSourceInit();        
    }

    /**
     * Get language information
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function info($key = false, $id = false)
    {
        $return = false;

        if($id) {
            $lang = self::$langs[$id];
        } else {
            $lang = self::$langs[self::$now];
        }
        
        if($key) {
            $return = isset($lang->$key) ? $lang->$key : false;
        } else {
            $return = $lang;
        }
        
        return $return;
    }

    public static function dbSourceInit() {
        $langfile = App::getSession('language');

        $defaultLanguage = self::getDefaultLanguageDBSource( $langfile );
        $currentLanguage = self::getLanguageDBSource( $langfile );

        $mergedArray = array_replace($defaultLanguage, array_filter($currentLanguage));

        self::$language = $mergedArray;
        
        return $mergedArray;        
    }

    public static function getLanguageDBSource( $langfile = 'en' ) {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `site_translations`
                WHERE `TranslationLanguage` = '".$langfile."'";

        $query = $db->prepare($sql);
        $query->execute();

        $data = array();
        while( $row = $query->fetch( PDO::FETCH_CLASS ) ){
            $key = trim($row->TranslationKey);
            $data[$key] = $row->TranslationValue;
        }
        unset($query);

        return $data;
    }

    public static function getLanguages() {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `languages`";

        $query = $db->prepare($sql);
        $query->execute();

        $data = array();
        while( $row = $query->fetch( PDO::FETCH_CLASS ) ){
            $data[] = $row;
        }
        unset($query);

        return $data;
    }

    public static function getLanguagesCode() {
        $db = new DBPDO();

        $sql = "SELECT LanguageCode 
                FROM `languages`";

        $query = $db->prepare($sql);
        $query->execute();

        $data = array();
        while( $row = $query->fetch( PDO::FETCH_CLASS ) ){
            $data[] = $row->LanguageCode;
        }
        unset($query);

        return $data;
    }

    public static function getDefaultLanguage() {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `languages`
                WHERE `Default` = 1";

        $data = $db->get_row($sql);        
        return $data;
    }

    public static function getOneLanguage($code) {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `languages`
                WHERE `LanguageCode` = '".$code."'";

        $data = $db->get_row($sql);
        
        return $data;
    }

    public static function getTheLanguage( $langfile = 'en' ) {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `site_translations`
                WHERE `TranslationLanguage` = '".$langfile."'";

        $query = $db->prepare($sql);
        $query->execute();

        $data = array();
        while( $row = $query->fetch( PDO::FETCH_CLASS ) ){
            $data[$row->TranslationKey] = $row;
        }
        unset($query);

        return $data;
    }

    public static function getDefaultLanguageDBSource() {
        $langfile = 'en';

        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `site_translations`
                WHERE `TranslationLanguage` = '".$langfile."'";

        $query = $db->prepare($sql);
        $query->execute();

        $data = array();
        while( $row = $query->fetch( PDO::FETCH_CLASS ) ){
            $key = trim($row->TranslationKey);
            $data[$key] = $row->TranslationValue;
        }
        unset($query);

        return $data;
    }

    public static function fileSourceInit() {
        self::$rootfile = "./app/".Config::get('TEMPLATE')."/language/";
        $langfile = App::getSession('language');
        
        if(file_exists(self::$rootfile.$langfile.self::ext)) {
            self::$package = Config::read(self::$rootfile.$langfile.self::ext);
        } else {
            $langfile = isset(App::$segment[2]) ? App::$segment[2] : 'en';
            if(file_exists(self::$rootfile.$langfile.self::ext)) {
                self::$package = Config::read(self::$rootfile.$langfile.self::ext);
            }
        }
    }
    
    public static function get($key='All', $default='', $filename=NULL) {
        
        $return = false;        
        $lang   = self::$package;
        $rootfile = "./app/".Config::get('TEMPLATE')."/language/";

        if( $filename == NULL) {
            $langfile = App::getSession('language');       
            $filename = ($langfile) ? $langfile : 'en';
        }

        $lang = Config::read(self::$rootfile.$filename.self::ext);
        
        if($key == 'All') {
            $return = $lang;
        } else {            
            $val = isset($lang[$key]) ? trim($lang[$key]) : '';
            $return = (strlen($val)) ? trim($val) : trim($default);
        }
        
        return $return;
    }

    public static function checkBeforeInsert($key,$lang='en') {
        $db = new DBPDO();

        $sql = "SELECT * 
                FROM `site_translations`
                WHERE `TranslationLanguage` = '".$lang."' AND `TranslationKey` = '".$key."'";

        return $db->get_row($sql);
    }

    public static function setNew($key,$value='') {
        $db = new DBPDO();

        if(strlen($value) > 0) {
            $exist = self::checkBeforeInsert($key,'en');

            if(!isset($exist->TranslationID)) {
                $data = array(
                    "TranslationLanguage" => 'en',
                    "TranslationKey"      => $key,
                    "TranslationValue"    => $value
                );

                return $db->insert('site_translations', $data);
            }

            return false;
        }
    }

    public static function db($key='All', $default='', $langnow = false) {
        
        $return = false;        
        $lang   = ($langnow) ? self::getLanguageDBSource($langnow) : self::$language;
        $key    = trim($key);

        if($key == 'All') {
            $return = $lang;
        } else {            
            $val = isset($lang[$key]) ? trim($lang[$key]) : '';
            $return = (strlen($val)) ? trim($val) : trim($default);

            if(strlen($val) == 0 && strlen($default) > 0) {
                self::setNew($key,$default);
            }
        }
        
        return $return;
    }

    public static function current() 
    {
        $segment   = App::$segment;
        $languages = self::getLanguagesCode();
        $default   = self::getDefaultLanguage()->LanguageCode;
        $langseg   = isset($segment[0]) ? $segment[0] : false;
        self::$now = $default;

        App::setSession('language', $default);
        if(in_array($langseg,$languages)) {
            App::setSession('language', $langseg);
            self::$now = $langseg;
        }
    }
}

Lang::init();