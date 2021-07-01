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
 * @package       Users info mapper
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class User
{
    /**
     * Get user information
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function info($key = false, $id = false)
    {
        $return = false;
        $db     = new DB();
        
        if( $id ){
            $where = "WHERE CONCAT(ul.Code,u.UserID) = '$id' LIMIT 1";
            
            if( ctype_digit($id) ){
                $where = "WHERE u.UserID = '".$id."' LIMIT 1";
            }

            $sql = "SELECT u.*, um.*, ul.Name as UserLevel, ul.Code, ul.Link as DashboardURL
                    FROM users u 
                    LEFT JOIN user_meta um ON um.UserID = u.UserID 
                    LEFT JOIN user_roles ul ON ul.RoleID = u.RoleID ".$where;
            
            $userdata = $db->queryOne($sql);
            $userinfo = ($userdata) ? (array) $userdata : array();
        } else {
            $usercookie = App::stringToArray( App::getCookie('userdata') );
            $keepme     = App::getCookie('keepmeloggedin');

            $userinfo   = ($keepme) ? $usercookie : App::getSession('userdata');
        }

        $userinfo = (array) $userinfo;
        
        if($key != false) {
            $return = isset($userinfo[$key]) ? $userinfo[$key] : false;
            if($key == "FullName") {
                $return = $userinfo["FirstName"] ." ". $userinfo["LastName"];
            }            
        } else {
            $return = ($userinfo) ? (object) $userinfo : array();
        }
        
        return $return;
    }
    
    /**
     * Get user information by email
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function infoByEmail($key = false, $email = false)
    {
        $return = false;
        $db     = new DB();
        
        if($email) {
            $sql = "SELECT u.*, um.*, ul.Name as UserLevel, ul.Code, ul.Link as DashboardURL 
                    FROM users u 
                    LEFT JOIN user_meta um ON um.UserID = u.UserID 
                    LEFT JOIN user_roles ul ON ul.RoleID = u.RoleID 
                    WHERE u.Email = '".$email."' LIMIT 1";
            
            $userdata = $db->queryOne($sql);
            $userinfo = ($userdata) ? (array) $userdata : array();
        } else {
            $usercookie = App::stringToArray( App::getCookie('userdata') );
            $keepme     = App::getCookie('keepmeloggedin');

            $userinfo   = ($keepme) ? $usercookie : App::getSession('userdata');
        }
        
        $userinfo = (array) $userinfo;
        
        if($key != false) {
            $return = isset($userinfo[$key]) ? $userinfo[$key] : false;
            if($key == "FullName") {
                $return = $userinfo["FirstName"] ." ". $userinfo["LastName"];
            }            
        } else {
            $return = ($userinfo) ? (object) $userinfo : array();
        }
        
        return $return;
    }

    /**
     * Get user information by sepcific field
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function infoBy($field = false, $value = false)
    {
        $return = false;
        $db     = new DB();
        
        if($field && $value) {
            $val = is_numeric($value) ? $value : "'".$value."'";
            $sql = "SELECT u.*, um.*, ur.Name as UserLevel, ur.Code, ul.Link as DashboardURL 
                    FROM users u 
                    LEFT JOIN user_meta um ON um.UserID = u.UserID 
                    LEFT JOIN user_roles ur ON ur.RoleID = u.RoleID 
                    WHERE ".$field." = ".$val." LIMIT 1";
            
            $return = $db->get_row($sql);
        }
        
        return $return;
    }

    /**
     * Get user level
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function role()
    {        
        return self::info('UserLevel');
    }

    /**
     * Get user level
     * 
     * @access public
     * @static
     *
     * @return string or object
     */
    public static function isLoggedIn()
    {
        return self::info('UserID') ? true : false;
    }
    
    /**
     * Get user capability
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function capabilities($id=false)
    {
        $return = false;      
        $userinfo = self::info('Capability',$id);               
        $return = App::jsonDecode($userinfo); 
        
        return $return;
    }
    
    /**
     * Check if user has capability
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function can($capability=false,$id =false)
    {
        $return = false;
        $capa = (array) self::capabilities($id);
        
        if($capability) {
            if(in_array($capability,$capa)) {
                $return = true;
            } else {
                $return = false;
            }
        }
        
        return $return;
    }

    /**
     * Check if user has capability
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function has($capas=array(),$id =false)
    {
        $return = false;
        $capa = (array) self::capabilities($id);
        
        if(count($capas)) {
            $return = false;
            foreach($capas as $c) {
                if(in_array($c,$capa)) {
                    $return = true;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Check user role
     * 
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function is($is=false,$id =false)
    {
        $return = false;
        $level = self::info('UserLevel',$id);

        if($level == $is) {
            $return = true;
        }
        
        return $return;
    }

    /**
     * Check user role
     * 
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function in($in=array(),$id =false)
    {
        $return = false;
        $level = self::info('UserLevel',$id);

        if(in_array($level, $in)) {
            $return = true;
        }
        
        return $return;
    }

    /**
     * Get user dashboard link
     * 
     * @access public
     * @static
     *
     * @return string
     */
    public static function dashboardLink( $urionly = false )
    {
        $return = false;
        $level  = self::info('RoleID');
        $theUrl = (self::is('Administrator')) ? Config::get('ADMIN_URI').'/' : Level::info('Link',$level);
        $return = ($urionly) ? $theUrl : Config::siteURL($theUrl);
        
        return $return;
    }
    
    /**
     * Get / Set cookie
     * 
     * @access public
     * @static
     *
     * @return void
     */
    public static function cookie( $name, $value = false)
    {
        $return = false;

        if( isset( $value ) ){
            setcookie($name, $value, 0, "/"); // 0: valid until the browser is closed.
        }
        
        if( isset( $_COOKIE[$name] ) ){ $return = $_COOKIE[$name]; }
        
        return $return;
    }
}