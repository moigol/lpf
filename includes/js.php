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
 * @package       JS processor
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class JS { 
    
    public static $segment;
    public static $uri;

    public static $headscript;
    public static $footscript;

    public static $headenqueue;
    public static $footenqueue;

    public static $customrule;

    public static $version;

    static public function init() 
    {
        $segment            = (Core::$segment) ? Core::$segment : array();

        self::$segment      = $segment; 
        self::$uri          = implode('/',$segment);
        self::$headscript   = array();
        self::$footscript   = array();  
        self::$headenqueue  = array();
        self::$footenqueue  = array();       
    }

    static public function sanitize($src='') 
    {
        $old = array('/','.');
        $new = array('-','-'); 
        return str_replace($old, $new, $src);      
    }

    static public function customRule( $rule = '' )
    {
        self::$customrule[] = $rule;
    }

    static public function register( $ID, $path = 'none.js', $inFooter = false  ) 
    {
        
        $jsID   = ($ID) ? strtolower($ID) : self::sanitize($path);
        $exists = file_exists(ASSETS_URI.$path) ? true : false;

        if($exists) {
            if($inFooter) {
                self::$footscript[$jsID] = $path;
            } else {
                self::$headscript[$jsID] = $path;
            }
        } else {
            $theurldata = parse_url($path);
            if(isset($theurldata['scheme']) && ($theurldata['scheme'] == 'http' || $theurldata['scheme'] == 'https')) {
                if($inFooter) {
                    self::$footscript[$jsID] = $path;
                } else {
                    self::$headscript[$jsID] = $path;
                }
            }
        }
    }

    static public function registers( $array = array() ) 
    {
        if( count($array) ) {
            foreach( $array as $ID => $args ) {
                $args_path  = isset($args[0]) ? $args[0] : false;
                $path       = isset($args['path']) ? $args['path'] : false;
                $path       = ($path) ? $path : $args_path;

                $args_foot  = isset($args[1]) ? $args[1] : false;
                $foot       = isset($args['infooter']) ? $args['infooter'] : false;
                $foot       = ($foot) ? $foot : $args_foot;

                self::register( $ID, $path, $foot );
            }
        }
    }

    static public function enqueue( $idArray = array(), $inFooter = false ) 
    {
        if(count($idArray)) {            
            $fscripts = self::$footscript; 
            self::$footenqueue = array();
            foreach($idArray as $id) {
                if(isset($fscripts[$id])) {
                    self::$footenqueue[$id] = $fscripts[$id];                        
                }
            }
        
            $hscripts = self::$headscript;
            self::$headenqueue = array();
            foreach($idArray as $id) {
                if(isset($hscripts[$id])) {
                    self::$headenqueue[$id] = $hscripts[$id];
                }
            }
          
        } 
    }

    static public function render( $inFooter = false ) 
    {
        $output = '';

        $scripts = (array) self::$headenqueue;
        $customs = (array) self::$customrule;

        if($inFooter) {
            $scripts = (array) self::$footenqueue;
        }

        if(count($customs)) {
            $output .= '<script>'."\n";
            foreach($customs as $rule) {    
                $output .= $rule."\n";
            }
            $output .= '</script>';
        }

        if(count($scripts)) {
            foreach($scripts as $id => $script) {

                $theurldata = parse_url($script);

                if(isset($theurldata['scheme']) && ($theurldata['scheme'] == 'http' || $theurldata['scheme'] == 'https')) {
                    $output .= '<script data-id="'.$id.'" src="'.$script.'"></script>';
                } else {
                    $v = self::$version;
                    $url = ($script) ? $script : '';  
                    $output .= '<script data-id="'.$id.'" src="'.Config::siteCDN(ASSETS_URI.$url.'?v='.$v).'"></script>';
                }
            }
        } 

        return $output;     
    }

    static public function headerOut( $echo = true ) 
    {
        if($echo) {
            echo self::render();
        } else {
            return self::render();
        } 
    }

    static public function footerOut( $echo = true ) 
    {
        if($echo) {
            echo self::render(true);
        } else {
            return self::render(true);
        } 
    }

    static public function clear($loc = 'all') 
    {
        switch(strtolower($loc)) {
            case 'head':
                 self::$headscript = array();
                 self::$headenqueue = array();
            break;
            case 'foot':
                 self::$footscript = array();
                 self::$footenqueue = array();
            break;
            case 'all':
            default:
                 self::$headscript = array();
                 self::$footscript = array();
                 self::$headenqueue = array();                 
                 self::$footenqueue = array();
            break;
        }
    }

    static public function clearEnqueue($loc = 'all') 
    {
        switch(strtolower($loc)) {
            case 'head':
                 self::$headenqueue = array();
            break;
            case 'foot':
                 self::$footenqueue = array();
            break;
            case 'all':
            default:
                 self::$headenqueue = array();                 
                 self::$footenqueue = array();
            break;
        }
    }
}
?>