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
 * @package       Core loader
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class Loader
{	
    private $_ext;

    function __construct()	
    {
        $this->_ext = Config::get('FILE_EXT');
    }

    function classNameSanitizer( $classname )
    {
        $rootclass = explode(DS, $classname);
        $lastkey = count($rootclass) - 1;
        
        return $rootclass[$lastkey];
    }

    function validate($classname = NULL)
    {
        $controllerSuffix = 'Controller';
        $classname .= $controllerSuffix;
        
        $this->fetcher(CONTROLLERS.$classname.DOT.$this->_ext, 'ro');

        $classname = $this->classNameSanitizer( $classname );

        if(class_exists($classname))
        {            
            return $classname;
        }
        else 
        {
            return false;
        }
    }
	
    function controller($classname = NULL)
    {
        $controllerSuffix = 'Controller';
        $classname .= $controllerSuffix;
        $this->fetcher(CONTROLLERS.$classname.DOT.$this->_ext, 'ro');

        $classname = $this->classNameSanitizer( $classname );

        if(class_exists($classname))
        {            
            return new $classname();
        }
        else 
        {
            return false;
        }
    }

    function subcontroller($parent = false, $classname = NULL)
    {
        $controllerSuffix = 'Controller';
        $classname       .= $controllerSuffix;
        $dir              = CONTROLLERS;
        if($parent) {
            $dir .= $parent .DS;
        }

        $this->fetcher($dir.$classname.DOT.$this->_ext, 'ro');

        if(class_exists($classname))
        {
            return new $classname();
        }
        else 
        {
            return false;
        }
    }

    function view($filename = NULL, $data = array())
    {
        $this->fetcher(VIEWS.$filename.DOT.$this->_ext, 'ro', $data);
    }	
    
    function vendor($filename = NULL, $data = array())
    {
        if(is_array($filename)) {
            foreach($filename as $file) {
                $this->fetcher(VENDOR.$file.DOT.$this->_ext, 'ro', $data);
            }
        } else {
            $this->fetcher(VENDOR.$filename.DOT.$this->_ext, 'ro', $data);
        }
    }

    function helper($filename = NULL, $data = array())
    {
        if(is_array($filename)) {
            foreach($filename as $file) {

                $helperfile = file_exists(HELPERS.$file.DOT.$this->_ext) ? HELPERS.$file.DOT.$this->_ext : RHELPERS.$file.DOT.$this->_ext;

                $this->fetcher($helperfile, 'ro', $data);
            }
        } else {
            $helperfile = file_exists(HELPERS.$filename.DOT.$this->_ext) ? HELPERS.$filename.DOT.$this->_ext : RHELPERS.$filename.DOT.$this->_ext;

            $this->fetcher($helperfile, 'ro', $data);
        }                
    }
            
    function model($modelname = NULL, $init = false, $suffix = false)
    {
        global $models;
        $modelnames = is_array($modelname) ? $modelname : array($modelname);
        foreach($modelnames as $modelname)
        {
            // sanitize model name
            $modelname  = $this->classNameSanitizer( $modelname );                   
            $modelcname = $modelname; 

            $adminModel = ROOTDIR . DS . ADMINFOLDER . DS.'models'.DS.$modelname.DOT.$this->_ext;

            $themed = file_exists(MODELS.$modelname.DOT.$this->_ext) ? MODELS.$modelname.DOT.$this->_ext : '';
            $themod = file_exists($adminModel) ? $adminModel : $themed;
                       
            if($this->fetcher($themod, 'ro')) {   
                $models[$modelname] = $modelcname;
            }
        }       
    }
        
    function fetcher($file = NULL, $load = 'ro', $data = array())
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
}