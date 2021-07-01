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
 * @package       Core
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

define( 'HASHPHRASE', Config::get('HASHPHRASE','lightphpframehashkey') );
define( 'SESSIONCODE', Config::get('SESSIONCODE','lightphpframesessionkey') );

class Core
{
    static $instance;
    static $get;
    static $post;
    static $file;
    static $controller;
    static $method;
    static $segment;
    static $error_msg;
    static $success_msg;

    static $view_dir;

    public static function init()
    {
        // TO DO: Aim to remove this init
        if($_REQUEST) {
            self::sanitizeRequest();
        }

        self::parseSegment();
    }

    public static function sanitizeRequest()
    {
        self::$get = isset($_GET) ? $_GET : NULL;
        self::$post = isset($_POST) ? $_POST : NULL;
        self::$file = isset($_FILES) ? $_FILES : NULL;

        return true;
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

    public static function pageSanitizer( $text )
    {
        return preg_replace("/[^a-zA-Z]+/", "", $text);
    }

    /**
     * Route the page classes
     *
     * @access  public
     * @return  object
     */
    public static function pageRouter()
    {
        global $models;

        $models          = array();
        self::$error_msg = false;
        $cname           = isset(self::$segment[0]) ? self::$segment[0] : Config::get('CONTROLLER');
        $appFolder       = $cname == Config::get('ADMIN_URI') ? ADMINFOLDER : APPFOLDER;
        
        /**
         *  Define constant for themes
         */
        define('APP', ROOTDIR . DS . $appFolder . DS);
        define('CONTROLLERS', APP .'controllers' . DS);
        define('MODELS', APP .'models' . DS);
        define('HELPERS', APP .'helpers' . DS);
        define('VIEWS', APP .'views' . DS);
        define('PAGES', APP .'views'. DS .'pages' . DS);
        define('ASSETS', APP .'views'. DS .'assets' . DS);
        define('IMAGES', APP .'views'. DS .'assets' . DS .'images' . DS);

        /**
         *  Define constant for uris
         */
        define('VIEWS_URI', $appFolder . RDS .'views'. RDS);
        define('ASSETS_URI', VIEWS_URI .'assets'. RDS);
        define('IMAGES_URI', ASSETS_URI .'images'. RDS);
        define('FILES_URI', ASSETS_URI .'files'. RDS);
        define('CSS_URI', ASSETS_URI .'css'. RDS);
        define('JS_URI', ASSETS_URI .'js'. RDS);

        $defaultControl = Config::get('CONTROLLER');
        $dynamicControl = Config::get('DYNAMICPAGECONTROLLER');
        if($appFolder == 'admin') {
            $defaultControl = Config::get('ADMIN_CONTROLLER');
            $dynamicControl = Config::get('ADMIN_CONTROLLER');
        }
        
        self::$method    = isset(self::$segment[1]) ? self::$segment[1] : NULL;
        $classname       = self::pageSanitizer( self::$controller === NULL ? $cname : self::$controller );
        $methodname      = self::$method === NULL ? 'index' : self::pageSanitizer(self::$method);
        $routed          = false;
        
        if( isset(Routes::$gets[self::$get['appsegment']]) ) {
            $classname  = Routes::$gets[self::$get['appsegment']]['class'];
            $methodname = Routes::$gets[self::$get['appsegment']]['method'];
            $routed     = 'GET';
        }

        if( isset(Routes::$posts[self::$get['appsegment']]) ) {
            $classname  = Routes::$posts[self::$get['appsegment']]['class'];
            $methodname = Routes::$posts[self::$get['appsegment']]['method'];
            $routed     = 'POST';
        }

        // Class doesnt exists
        if( self::load()->validate($classname) === false ) {
            $methodtemp = $classname;
            $classname = $defaultControl;
            
            if($loadedClass = self::load()->validate($classname)) {
                // Default
                if(method_exists($loadedClass,$methodtemp)) {
                    $classname = $defaultControl;
                    $methodname = $methodtemp;
                } else {
                    // Use dynamic page routed in default controller with index method
                    $classname = $dynamicControl;
                    $methodname = 'index';
                }
            } else {
                self::$error_msg = 'Unable to locate class.';
                $classname = Config::get('NOPAGE');
                $methodname = 'index';
            }
        }

        

        $loadedClass = self::load()->controller($classname);

        if($loadedClass) {
            // Sanitize path like classname loaded from routes
            $classname = self::load()->classNameSanitizer( $classname );

            // Auto assign controller's model
            self::load()->model($classname, true);

            if(!$loadedClass === false)
            {
                foreach($models as $k => $model)
                {
                    $modelName      = strtolower($model);
                    $modelNameCall  = $modelName;
                    $modelKey       = $k;

                    if(strtolower($classname) == $modelName)
                    {
                        $modelNameCall  = $modelName;
                        $modelKey       = 'model';
                    }
                }

                if(method_exists($loadedClass,$methodname)) {
                    if(self::$error_msg) {
                        $loadedClass->index(self::$error_msg);
                    } else {
                        switch($routed) {
                            case 'GET':
                                $getValues = self::$get;
                                unset($getValues['appsegment']);
                                $loadedClass->$methodname( $getValues, self::$segment );
                            break;
                            case 'POST':
                                $loadedClass->$methodname( self::$post, self::$segment );
                            break;
                            default:
                                $loadedClass->$methodname();
                            break;
                        }
                    }
                } else {
                    $loadedClass->index('Unable to locate method in class.');
                }
            }
        }
    }

    public static function load()
    {
        if(class_exists('Loader'))
        {
            return new Loader();
        }
    }
}  
