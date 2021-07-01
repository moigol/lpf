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
 * @package       Core controller mapper
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Controller
{
        
    public function __construct()
    {
        // Assign the load system to the current controller
        $this->load    = App::load();
        $this->segment = Core::$segment;
        $this->post    = Core::$post;
        $this->get     = Core::$get;
        $this->file    = Core::$file;
    }

    public function controlSwitch( $method, $prefix = 'mpf', $params = array() ) 
    {
        $callMethod = $prefix.ucfirst($method);
        if(method_exists($this,$callMethod) && $method != '') {
            return $this->$callMethod( $params );
        } else {
            return false;
        }
    }
    
    public function loadController( $class, $method, $params = array() ) 
    {   
        $subsegment = App::$segment;
        $segmentkey = array_search($class, $subsegment);

        $segmentout = $segmentkey -1;

        if($this->load->validate($class) !== false) {
            $theclass    = $this->load->controller($class);
            $defaultseg  = isset($subsegment[1]) ? $subsegment[1] : 'main';
            View::$theme = !in_array($subsegment[0], ['en','de','es','ph']) ? $subsegment[0] : $defaultseg; 

            for($s = $segmentout; $s >= 0; $s--) {
                unset($subsegment[$s]);                    
            }

            if($loadedModel = $this->load->model($class, true)) {
                $theclass->model = $loadedModel;
            }
            
            $theclass->segment = array_values($subsegment);
            $callMethod        = strtolower($method);            
            
            if(method_exists($theclass,$callMethod) && $method != '') {
                return $theclass->$callMethod( $params );
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}