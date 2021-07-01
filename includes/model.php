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
 * @package       Model mapper
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Model
{
    static $db;
    static $table;    
    static $fields;
    static $ID;
    static $dateDeletedField;
    static $dateFormat;

    static public function init()
    {
        self::$dateFormat = 'Y-m-d H:i:s';

        return get_called_class();
    }

    static public function db()
    {
        $return = false;
        
        if(class_exists('DB'))
        {
            $return = new DB();
        }

        return $return;
    }

    static public function all($args = array())
    {
        $default = [
            'where' => false,
            'group' => false,
            'field' => false,
            'index' => false
        ];

        extract(App::mergeArgs($default, $args));

        $class = self::init();
        $db = self::db();
        $db->select($class::$fields);
        $db->from($class::$table);

        if($where) {
            $db->where($where);
        }

        return $db->all($group, $field, $index);
    }

    static public function one($ID)
    {
        $class = self::init();
        return self::db()->
                     select($class::$fields)->
                     from($class::$table)->
                     where([$class::$ID => $ID])->
                     one();
    }
    
    static public function find( $where = '1' )
    {
        return self::all( ['where' => $where] );
    }

    static public function findOne( $where = '1' )
    {
        $class = self::init();
        return self::db()->
                     select($class::$fields)->
                     from($class::$table)->
                     where($where)->
                     one();
    }

    static public function add($data = array(), $keys = array())
    {
        $class = self::init();
        return self::db()->insert( $class::$table, $data, $keys );
    }

    static public function update($data = array(), $ID = false)
    {
        $class = self::init();

        if($ID) {
            $where = [$class::$ID => $ID];
        }

        return self::db()->update( $class::$table, $data, $where );
    }

    static public function updateWhere($data = array(), $where = false)
    {
        $class = self::init();

        if($where) {
            return self::db()->update( $class::$table, $data, $where );
        }
    }

    static public function softDelete($ID)
    {
        $class = self::init();
        $data  = [$class::$dateDeletedField => date($class::$dateFormat)];
        $where = [$class::$ID => $ID];
        return self::db()->update( $class::$table, $data, $where );
    }

    static public function delete($ID)
    {
        $class = self::init();
        return self::db()->delete( $class::$table, [$class::$ID => $ID] );
    }

    static public function deleteWhere($where = false)
    {
        $class = self::init();
        return self::db()->deleteWhere( $class::$table, $where );
    }
}