<?php 
class Users extends Model 
{
    static $table  = 'users';    
    static $fields = false;
    static $ID     = 'UserID';     

    /*
     * Add custom query method below 
     * - When there is more than one table
     * - When its out of scope in the table defined above
     */

    // Get all users
    public static function getAll()
    {
        $fields = "users.UserID,
                   users.Email,
                   users.RoleID as Level,
                   users.DateAdded,
                   user_meta.FirstName,
                   user_meta.LastName,
                   user_roles.Name as LevelName,
                   user_roles.Code,
                   users.Active,
                   (SELECT Slug FROM media_files WHERE ElementID = users.UserID AND Description = 'Avatar' ORDER BY DateAdded DESC LIMIT 1) as AvatarSlug";
        
        $where = "users.Active = 1 AND users.DateDeleted IS NULL";
        
        return self::db()
                    ->select($fields)
                    ->from(self::$table)
                    ->leftJoin('user_meta', self::$ID)
                    ->leftJoin('user_roles', 'RoleID')
                    ->where($where)
                    ->all();
    }

    // Get one user by ID
    public static function getOne( $ID )
    {        
        $where = "users.UserID = ". $ID;
        
        return self::db()
                    ->from(self::$table)
                    ->leftJoin('user_meta', self::$ID)
                    ->where($where)
                    ->one();
    }
}