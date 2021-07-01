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
 * @package       assets helper
 * @version       LightPHPFrame v1.1.10
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Asset {

    static $defaultcss = array();
    static $defaultjs = array();

    // Register enqueue scripts
    static public function init()
    {
        JS::$version = '1.0.01';
        CSS::$version = '1.0.01';
        
        self::registerCSS();
        self::registerJS();
    }

    static public function registerCSS()
    {
        // Clear assets list incase it was previously registered
        Assets::clear('css');

        // User bulk register by folder and not the whole css folder
        Assets::bulkRegister( 'css/', 'css' );
    }

    static public function registerJS()
    {
        Assets::clear('js');

        // User bulk register by folder and not the whole js folder
        Assets::bulkRegister('js/', 'js', true);
    }

    // Load enqueue scripts
    static public function load( $newcss = array(), $newjs = array() )
    {
        $css = array_merge(self::$defaultcss,$newcss);
        $js = array_merge(self::$defaultjs,$newjs);

        // Load
        Assets::enqueue('css', $css );
        Assets::enqueue('js', $js );
    }
}

Asset::init();
?>
