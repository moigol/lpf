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
 * @package       Asset mapper
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once BOOTSTRAP.'css.php';
require_once BOOTSTRAP.'js.php';

class Assets { 
    
    static public function init() 
    {
        JS::init();
        CSS::init();
    }

    static public function inline( $identifier = '#MPF_general_rule', $rule = '' )
    {
        CSS::customRule( $identifier, $rule );
    }

	static public function js( $ID, $path = 'none.js', $inFooter = false )
    {
        JS::register( $ID, $path, $inFooter );
    }    

    static public function jsBulk( $array = array() )
    {
        JS::registers( $array );
    } 

    static public function css( $ID, $path = 'none.css', $inFooter = false )
    {
        CSS::register( $ID, $path, $inFooter );
    }

    static public function cssBulk( $array = array() )
    {
        CSS::registers( $array );
    }

    static public function getBulkAssets($folder = 'js', $useparent = false)
    {
        $dir = ASSETS_URI.$folder;
        $return = array();


        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::SELF_FIRST); 
        
        foreach($rii as $file) {            
            if($file->isDir()) { 
                continue;
            }

            $filename = strtolower( pathinfo($file->getPathname(), PATHINFO_FILENAME) );
            $realpath = realpath($file->getPathname());
            $filebase = explode('assets/', $realpath);
            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', $filebase[1]);
            
            $return[$filename]['name'] = $filename;
            $return[$filename]['file'] = $filepath;
        }

        return $return;
    }

    static public function bulkRegister($folder = 'js', $type = 'js', $inFooter = false) 
    {
        $assets = self::getBulkAssets($folder);
        foreach($assets as $asset) {
            self::$type( $asset['name'], $asset['file'], $inFooter );
        }
    }

    static public function enqueue( $type = 'js', $idArray = array(), $inFooter = false )
    {
    	switch(strtolower($type)) {
    		case 'css':
    			CSS::enqueue( $idArray, $inFooter );
    		break;
    		case 'js':
    		default:
    			JS::enqueue( $idArray, $inFooter );
    		break;
    	}
    }

    static public function clear( $type = 'js', $loc = 'all' ) 
    {
        switch(strtolower($type)) {
    		case 'css':
    			CSS::clear( $loc );
    		break;
    		case 'js':
    		default:
    			JS::clear( $loc );
    		break;
    	}
    }

    public static function header()
    {
        CSS::headerOut();
        JS::headerOut();
    }
    
    public static function footer()
    {        
        CSS::footerOut();
        JS::footerOut();
    }
}

Assets::init();
?>