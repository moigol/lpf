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
 * @package       core
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
// Global functions

function p( $data, $ret = false, $kill = false, $hidden = false ) 
{
    echo '<pre style="display:'. (($hidden) ? 'none' : 'block') .'">';
    print_r( $data, $ret );
    echo '</pre>';

    if($kill) {
        die();
    }
}

function asset($thefile=false, $echo=true)
{
    View::asset($thefile, $echo);
}

function getHeader($folder=false)
{        
    View::header($folder);
}

function getFooter($folder=false)
{
    View::footer($folder);
}

function sidebar($folder=false)
{
    View::sidebar($folder);
}

function view($filename = NULL, $data = array())
{
    View::page($filename, $data);
}

function block($filename = NULL, $data = array())
{
    View::block($filename, $data);
}

function load($type = 'model', $filename = NULL, $data = array())
{
    App::load()->$type($filename, $data);
}