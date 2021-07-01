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
 
// Start php session
session_start();

/**
 *  Define version
 */
define('APPNAME', 'LightPHPFrame');
define('APPVERSION', '1.0.0');

define('APPFOLDER', 'app');
define('ADMINFOLDER', 'appadmin');

/**
 *  Define constant for directories
 */
define('ROOTDIR', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('RDS', '/');
define('DOT', '.');
define('VENDOR', ROOTDIR  . DS .'vendor' . DS);
define('LANGUAGE', ROOTDIR  . DS .'language' . DS);
define('RHELPERS', ROOTDIR  . DS .'helpers' . DS);
define('RASSETS', ROOTDIR  . DS .'assets' . DS);
define('BOOTSTRAP', ROOTDIR . DS . 'includes' . DS);
define('DATABASE', BOOTSTRAP .'database' . DS);

/**
 *  Define constant for uris
 */
define('ASSETS_PURI', 'assets'. RDS);
define('IMAGES_PURI', ASSETS_PURI .'images'. RDS);
define('FILES_PURI', ASSETS_PURI .'files'. RDS);
define('CSS_PURI', ASSETS_PURI .'css'. RDS);
define('JS_PURI', ASSETS_PURI .'js'. RDS);
define('UPLOADS_PURI', ASSETS_PURI .'uploads'. RDS);

// Load database
require_once DATABASE  .'dbpdo.php';
require_once DATABASE  .'db.php';

// Load bootstrap
require_once BOOTSTRAP .'routes.php';
require_once BOOTSTRAP .'config.php';
require_once BOOTSTRAP .'loader.php';
require_once BOOTSTRAP .'core.php';
require_once BOOTSTRAP .'app.php';
require_once BOOTSTRAP .'media.php';
require_once BOOTSTRAP .'controller.php';
require_once BOOTSTRAP .'model.php';
require_once BOOTSTRAP .'assets.php';
require_once BOOTSTRAP .'level.php';
require_once BOOTSTRAP .'user.php';
require_once BOOTSTRAP .'view.php';
require_once BOOTSTRAP .'language.php';

// Global functions
require_once BOOTSTRAP .'functions.php';

if(!class_exists('LightPHPFrame'))
{
    class LightPHPFrame
    {
        function __construct()	
        {
            // Initialize app
            Config::errorReporting();
            Core::init();
            Core::pageRouter();	
        }
    }

    new LightPHPFrame();
}