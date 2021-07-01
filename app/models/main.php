<?php 
class Main extends Model {

    static $table  = 'users';    
    static $fields = 'UserID, Email, UserLevelID';
    static $ID     = 'UserID'; 

    static function getAll() 
    {
        self::db()->select(self::$fields)->from(self::$table)->all();
    }
}