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

class DB
{
    var $table;
    var $fields;
    var $primaryKey;
    var $db;

    var $select     = "";
    var $from       = "";
    var $updatefrom = "";
    var $setfields  = "";
    var $where      = "";
    var $group      = "";
    var $order      = "";
    var $orderby    = "";
    var $limit      = "";
    var $join       = array();
    var $ljoin      = array();

    function __construct()
    {
        // Initialize DB
        if(class_exists('DBPDO'))
        {
            $this->db = new DBPDO();
        }
    }

    function query( $sql, $keyField = false, $returnField = false, $index = false )
    {
        $query = &$this->db->prepare($sql);
        $query->execute();
        $data = array();
        while ($row = $query->fetch(PDO::FETCH_CLASS)){
            if( $keyField ) {
                if(is_array($keyField)) {
                    if(isset($keyField[0]) && isset($keyField[1]) && isset($keyField[2])) {
                        $key1 = $keyField[0];
                        $key2 = $keyField[1];
                        $key3 = $keyField[2];
                        $data[$row->$key1][$row->$key2][$row->$key3][] = ($returnField) ? $row->$returnField : $row;
                    }
                    if(isset($keyField[0]) && isset($keyField[1])) {
                        $key1 = $keyField[0];
                        $key2 = $keyField[1];
                        $data[$row->$key1][$row->$key2][] = ($returnField) ? $row->$returnField : $row;
                    }
                    if(isset($keyField[0]) && count($keyField) == 1) {
                        $key1 = $keyField[0];
                        $data[$row->$key1][] = ($returnField) ? $row->$returnField : $row;
                    }
                } else {
                    if($index) {
                        $data[$row->$keyField][$row->$index] = ($returnField) ? $row->$returnField : $row;
                    } else {
                        $data[$row->$keyField][] = ($returnField) ? $row->$returnField : $row;
                    }
                }
            } else {
                if($index) {
                    $data[$row->$index] = ($returnField) ? $row->$returnField : $row;
                } else {
                    $data[] = ($returnField) ? $row->$returnField : $row;
                }
            }                     
        }
        unset($query);
        
        return $data;
    }

    function queryOne( $sql )
    {   
        // Single row
        return $this->db->get_row($sql);
    }

    function select( $select = "" )
    {   
        $this->select = $select;
        return $this;
    }

    function from( $from )
    {   
        $this->from = $from;
        return $this;
    }

    function updateFrom( $from ) 
    {
        $this->updatefrom = $from;
        return $this;
    }

    function convertArrayToQuery( $whereArray = array(), $operator = "=", $condition = "OR" )
    {   

        $table = isset($this->from) && ($this->from) ? $this->from."." : "";
        $whereValues = array();
        $opstart = $operator == 'IN' ? '(' : '';
        $opend   = $operator == 'IN' ? ')' : '';
        foreach( $whereArray as $key => $val ) {
            if( is_int($val) ) {
                $whereValues[] = $table.$key .' '.$operator.' '.$opstart.$val.$opend;
            } elseif(is_array($val)) {
                $newarray = [];
                foreach ($val as $v) {
                    if(is_numeric($v)) {
                        $newarray[] = $v;
                    } else {
                        $newarray[] = "'".$v."'";
                    }
                }
                $inarray = implode(",",$newarray);
                $whereValues[] = $table.$key ." ".$operator." ".$opstart.$inarray.$opend;
            } else {
                $whereValues[] = $table.$key ." ".$operator." ".$opstart."'". $val ."'".$opend;
            }
        }

        $return = implode(' '.$condition.' ', $whereValues);

        return $return;
    }

    function setFields( $setfields = "", $operator = '=' ) 
    {
        $this->setfields = $setfields;

        if(is_array($setfields)) {
            $this->setfields = $this->convertArrayToQuery( $setfields, $operator, ',' );
        }
        
        return $this;
    }

    function where( $where = "", $operator = '=', $condition = 'OR' )
    { 
        $this->where = $where;

        if(is_array($where)) {
            $this->where = $this->convertArrayToQuery( $where, $operator, $condition );
        }
        
        return $this;
    }

    function limit( $limit )
    { 
        $this->limit = $limit;
        return $this;
    }

    function join( $jointable, $joinfield, $joinfield2 = false )
    { 
        $this->join[] = array('table'=>$jointable,'field'=>$joinfield,'fieldtwo'=>$joinfield2);
        return $this;
    }

    function leftJoin( $jointable, $joinfield, $joinfield2 = false  )
    { 
        $this->ljoin[] = array('table'=>$jointable,'field'=>$joinfield,'fieldtwo'=>$joinfield2);
        return $this;
    }

    function group( $group )
    { 
        $this->group = $group;
        return $this;
    }

    function order( $orderby, $order )
    { 
        $this->orderby = $orderby;
        $this->order = $order;
        return $this;
    }

    function resetKeys() 
    {
        $this->select     = "";
        $this->from       = "";
        $this->updatefrom = "";
        $this->setfields  = "";
        $this->where      = "";
        $this->group      = "";
        $this->order      = "";
        $this->orderby    = "";
        $this->limit      = "";
        $this->join       = array();
        $this->ljoin      = array();
    }

    function prepare()
    { 
        $sql = "";

        if(isset($this->from) && ($this->from)) {
            $select = isset($this->select) && ($this->select)   ? "SELECT ". $this->select                      : "SELECT * ";
            $from   = isset($this->from) && ($this->from)       ? " FROM ". $this->from                          : "FROM ". $this->$table;
            $where  = isset($this->where) && ($this->where)     ? " WHERE ". $this->where                        : "";
            $group  = isset($this->group) && ($this->group)     ? " GROUP BY ". $this->from.".".$this->group                      : "";
            $order  = isset($this->orderby) && ($this->orderby) ? " ORDER BY ". $this->from.".". $this->orderby ." ".$this->order : "";
            $limit  = isset($this->limit) && ($this->limit)     ? " LIMIT ".$this->limit                                          : "";
            
            $joinsql = "";
            if( isset($this->join) && count($this->join) ) {
                foreach($this->join as $join) {
                    $joinfield = ($join['fieldtwo']) ? $join['fieldtwo'] : $join['field'];
                    $joinsql .= " JOIN ". $join['table'] ." ON ". $this->from .".". $join['field'] ." = ". $join['table'] .".". $joinfield ." ";
                }
            }

            $ljoinsql = "";
            if( isset($this->ljoin) && count($this->ljoin) ) {
                foreach($this->ljoin as $ljoin) {
                    $joinfield = ($ljoin['fieldtwo']) ? $ljoin['fieldtwo'] : $ljoin['field'];
                    $ljoinsql .= " LEFT JOIN ". $ljoin['table'] ." ON ". $this->from .".". $ljoin['field'] ." = ". $ljoin['table'] .".". $joinfield ." ";
                }
            }

            $this->resetKeys();

            $sql = $select.$from.$joinsql.$ljoinsql.$where.$group.$order.$limit;
        } elseif(isset($this->updatefrom) && ($this->updatefrom)) {
            $from   = isset($this->updatefrom) && ($this->updatefrom) ? "UPDATE `". $this->updatefrom."`"  : "";
            $set    = isset($this->setfields) && ($this->setfields)   ? " SET ". $this->setfields." "      : "";
            $where  = isset($this->where) && ($this->where)           ? " WHERE ". $this->where            : "";

            $this->resetKeys();
            
            $sql = $from.$set.$where;
        }

        return $sql;
    }

    function out($echo = false)
    { 
        $sql = $this->prepare(); 
        if($echo) {
            echo $sql;
        } else {
            return $sql;
        }
    }

    function all( $keyField = false, $returnField = false, $index = false )
    { 
        $sql    = $this->prepare(); 
        $data   = $this->query( $sql, $keyField, $returnField, $index );
        
        return $data;
    }

    function one()
    { 
        $sql    = $this->prepare();
        $data   = $this->queryOne($sql);
        
        return $data;
    }

    function go()
    { 
        $sql    = $this->prepare(); 
        $data   = $this->query($sql);
        
        return $data;
    }

    function sub( $as = false )
    { 
        $sql = "(";
        $sql .= $this->prepare();
        $sql .= ")";

        if($as) {
            $sql .= " as ".$as;
        }

        return $sql;
    }

    function insert( $table, $data = array(), $keys = array() ) 
    {
        if(count($keys)) {
            return $this->db->insert_update( $table, $data, $keys );
        } else {
            // Return ID of inserted data
            return $this->db->insert( $table, $data );
        }
    }

    function update( $table, $data = array(), $where = array() ) 
    {
        // Insert
        return $this->db->update( $table, $data, $where );
    }

    function delete( $table, $where = array() ) 
    {
        // Insert
        return $this->db->delete( $table, $where );
    }

    function deleteWhere( $table, $where ) 
    {
        if (! is_array($where)) {
            $thewhere = $where;
        } else {
            
            $wheres = array();
            foreach ((array) array_keys($where) as $field) {
                $wheres[] = "`$field` = ?";
            }

            $thewhere = implode(' AND ', $wheres);
        }

        $sql = "DELETE FROM ". $table  ." WHERE ".$thewhere;

        return $this->db->query( $sql );
    }
 
    function getColumns( $fieldOnly = false, $table = false )
    {
        $output = array();

        if(!$table) {
            $table = $this->table;
        }

        $data = $this->query("SHOW COLUMNS FROM `".$table."`");

        if($fieldOnly) {
            foreach($data as $d) {
                $output[] = $d->Field;
            }
        } else {
            $output = $data;
        }        
        
        return $output;
    }

    function getPrimaryKey( $firstOnly = false, $table = false )
    {
        $output = array();

        if(!$table) {
            $table = $this->table;
        }

        if($firstOnly) {
            $output = $this->queryOne("SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'");
        } else {
            $output = $this->query("SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'");
        }        
        
        return $output;
    }
}