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
 * @package       Level mapper
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
class Level
{
    static $roles;
    
    public static function init()
    {
        $return = false;
        $db = new DB();
        self::$roles = $db->from('user_roles')->all(false, false, 'RoleID');
    }

    /**
     * Get level information
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function info($key = false, $id = 1)
    {
        $return = false;
        $roles  = self::$roles;
        
        if($key) {
            $return = isset($roles[$id]->$key) ? $roles[$id]->$key : false;
        } else {
            $return = $roles[$id];
        }
        
        return $return;
    }
    
    /**
     * Get level capability
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function capabilities($id = 5)
    {
        $return = false;        
        $userinfo = self::info('Capability',$id);               
        $return = App::stringToArray($userinfo); 
        
        return $return;
    }
    
    /**
     * Get specific user capability
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function can($capability=false,$id = 5)
    {
        $return = false;
        $capa = self::capabilities($id);
        
        if($capability) {
            if(in_array($capability,$capa)) {
                $return = true;
            } else {
                $return = false;
            }
        }
        
        return $return;
    } 
}

Level::init();