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
 * @package       URI to class router
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
class Routes
{
    public static $posts;
    public static $gets;

    static public function init() 
    {
        self::$posts  = array();
        self::$gets   = array();       
    }
    
    static public function get( $uri, $class, $method )
    {
        $key = self::sanitizeUri($uri);
        self::$gets[$uri] = array(
                                    'uri' => $uri,
                                    'segment' => explode('/',$uri),
                                    'class' => $class,
                                    'method' => $method
                                );
    }
    
    static public function post( $uri, $class, $method )
    {
        $key = self::sanitizeUri($uri);
        self::$posts[$uri] = array(
                                    'uri' => $uri,
                                    'segment' => explode('/',$uri),
                                    'class' => $class,
                                    'method' => $method
                                );
    }

    static public function sanitizeUri( $uri )
    {
        return str_replace( '/', '_', $uri);
    }

    static public function desanitizeUri( $uri )
    {
        return str_replace( '_', '/', $uri);
    }
}

Routes::init();

require_once ROOTDIR . DS .'router.php';