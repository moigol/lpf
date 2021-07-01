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
 * @package       Configurations
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 *  Include the necessary configuration files
 */
class Config
{
    const configfile = "./.env";
    
    // Set a global styles and scripts
    static $scripts = array();
    static $styles = array();    
    static $footerscripts = array();
    static $footerstyles = array();

    /**
     * Open configuration file
     *
     * @access  public, static
     * @return  array
     */
    public static function open()
    {        
        $mpf_file = file_exists(self::configfile) ? self::configfile : "./.env";
        $file = file_get_contents($mpf_file, FILE_USE_INCLUDE_PATH);
        
        return $file;
    }    

    /**
     * Update configuration file
     *
     * @access  public, static
     * @return  array
     */
    public static function update( $value = false )
    {        
        $mpf_file = file_exists(self::configfile) ? self::configfile : "./.env";
        
        if($value) {            
            $file_handle = fopen($mpf_file, 'w'); 
            fwrite($file_handle, $value);
            fclose($file_handle);
        }

        return true;
    }  
    
    /**
     * Read configuration file
     *
     * @access	public, static
     * @return	array
     */
    public static function read($filename)
    {
        $config = array();
        
        $myfile = fopen($filename, "r") or die("Unable to open file!");
        // Output one line until end-of-file
        while($line = fgets($myfile)) {
            if(strlen($line)) {
                $set = explode('=',$line,2);
                if(isset($set[0])) {
                    $config[$set[0]] = isset($set[1]) ? $set[1] : '';
                }
            }
        }
        
        fclose($myfile);
        
        return array_filter($config);
    }
    
    /**
     * Write configuration file
     *
     * @access	public, static
     * @return	void
     */
    public static function write($filename, array $config)
    {
        $config = http_build_query($config,'',"\r\n");
        file_put_contents($filename, "$config\r\n");
    }
    
    /**
     * Get configuration value
     *
     * @access	public, static
     * @return	string
     */
    public static function get($key='All', $default='')
    {
        $mpf_file = file_exists(self::configfile) ? self::configfile : "./.env";
        $conf = self::read($mpf_file);
        
        if($key == 'All') {
            $return = $conf;
        } else {            
            $val = isset($conf[$key]) ? trim($conf[$key]) : '';
            $return = (strlen($val)) ? trim($val) : trim($default);
        }
        
        return $return;
    }
    
    /**
     * Set configuration value
     *
     * @access	public, static
     * @return	void
     */
    public static function set($key='All', $val='')
    {
        if($key == 'All') {
            if(is_array($val)) {
                self::write(self::configfile,$val);
            }
        } else { 
            $conf = self::read(self::configfile);
            if($val) {
                $conf[$key] = $val;
            }

            self::write(self::configfile,$conf);
        }
    }

    /**
     * get Site URL
     *
     * @access  public, static
     * @return  void
     */
    public static function siteURL($path = false)
    {
        // get url from config file .env
        return strtolower(self::get('URL')) . $path;
    }

    public static function siteCDN($path = false)
    {
        $url = ($path) ? $path : '';
        $outURL = self::get('SITE_CDN') == 'true' ? self::get('SITE_CDN_URL') .$url : self::siteURL($url);

        return $outURL; 
    }

    /**
     * Error reporting
     *
     * @access	public, static
     * @return	void
     */
    public static function errorReporting()
    {
        $debug = strtolower(self::get('DEBUG'));

        switch($debug)
        {
            case "all":
            {
                ini_set("display_errors", true);
                ini_set("error_reporting", E_ALL);
            } break;
            case "true":
            {
                ini_set("display_errors", true);
                ini_set("error_reporting", 1);
            } break;
            default:
            {
                ini_set("display_errors", false);
                ini_set("error_reporting", 0);
            } break;
        }
    }

    /**
     * Error reporting
     *
     * @access	public, static
     * @return	void
     */
    public static function DBInfo()
    {
        $dbinfo = (object) [];

        $dbinfo->host     = Config::get('DB_HOST','localhost');
        $dbinfo->user     = Config::get('DB_USER','');
        $dbinfo->pass     = Config::get('DB_PASS','');
        $dbinfo->database = Config::get('DB_NAME','');
        $dbinfo->prefix   = Config::get('DB_PREFIX','');
        $dbinfo->encoding = Config::get('DB_ENCODING','utf8');

        return $dbinfo;
    }

    /**
     * Write configuration file
     *
     * @access  public, static
     * @return  void
     */
    public static function log($text)
    {
        if(self::get('DB_LOG') == 'true') {
            //Something to write to txt log
            $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
                    "Logs: ".$text.PHP_EOL.
                    "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('./logs/'.date("j.n.Y").'.log', $log, FILE_APPEND);
        }
    }
}