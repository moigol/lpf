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
 * @package       Media utility functions
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Media
{
    static $db;
    static $table = "media_files";
    static $keyid = "MediaID";
    static $elemi = "ElementID";
    static $descr = "Description";
    static $imgs  = ['png','jpeg','jpg','ico','gif'];

    public static function init()
    {
        // init function
        // self::$db = new DB(); // Make db as class property
    }

    public static function move( $file, $data, $public = false, $custom_filename = false, $folder = '' ) 
    {
        $db        = new DB();
        $return    = false;  
        $rootPath  = (($public) ? RASSETS : ASSETS) .'uploads';
        $paths     = self::path( $rootPath );
        $filePath  = $paths['abs'];
        $subFolder = $paths['sub'];

        if (strlen($file['name'])) {
            if($file['error'] > 0){
            } else {
                $timestamp = time();
                $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
                $fnm = pathinfo( $file['name'], PATHINFO_FILENAME );

                $tempName = strtolower( basename($file['name']) );
                
                $fileName = ($custom_filename) ? 
                                $custom_filename : 
                                str_replace( ' ', '', $timestamp .'_'. $tempName );

                if (move_uploaded_file($file['tmp_name'], $filePath . $fileName)) {
                    $data['FileName'] = $fileName;
                    $data['Path'] = $filePath . $fileName;  
                    $data['Slug'] = str_replace(DS,'/',$subFolder) . $fileName;  

                    $return = $db->insert(self::$table, $data);     

                }
            }
        }

        return $return;
    }

    public static function path( $rootPath )
    {
        $year = date('Y');

        if (!file_exists($rootPath.DS.$year)) {
            mkdir($rootPath.DS.$year, 0777, true);
            chmod($rootPath.DS.$year, 0777);
        }

        $month = date('m');

        if (!file_exists($rootPath.DS.$year.DS.$month)) {
            mkdir($rootPath.DS.$year.DS.$month.DS, 0777, true);
            chmod($rootPath.DS.$year.DS.$month.DS, 0777);
        }

        $day = date('d');

        if (!file_exists($rootPath.DS.$year.DS.$month.DS.$day)) {
            mkdir($rootPath.DS.$year.DS.$month.DS.$day.DS, 0777, true);
            chmod($rootPath.DS.$year.DS.$month.DS.$day.DS, 0777);
        }

        $subFolder = DS.$year.DS.$month.DS.$day.DS;

        return [ 'abs' => $rootPath.$subFolder, 'sub' => $subFolder];
    }

    public static function upload($files = false, $ID = NULL, $Description = NULL, $public = false, $custom_filename = false, $folder = '')
    {
        $return = [];
        $files  = ($files) ? $files : $_FILES;                

        foreach($files as $key => $file) 
        {
            if (is_array($files[$key]['name'])) {
                $num_files = count($files[$key]['tmp_name']);
                
                for( $i=0; $i < $num_files; $i++ )
                {
                    $data[self::$elemi]   = $ID;
                    $data[self::$descr] = $Description;

                    $file = [
                        'name' => $files[$key]['name'][$i],
                        'error' => $files[$key]['error'][$i],
                        'tmp_name' => $files[$key]['tmp_name'][$i]
                    ];

                    $return[$key][] = self::move( $file, $data, $public, $custom_filename, $folder );
                }

            } else {

                $data[self::$elemi]   = $ID;
                $data[self::$descr] = $Description;

                $return[$key] = self::move( $files[$key], $data, $public, $custom_filename, $folder );
            }
        }

        return $return;
    }

    public static function getAll($ID = NULL, $Description = 'Avatar')
    {
        if($ID) {
            $db   = new DB();
            $data = $db->from(self::$table)->where("`DateDeleted` IS NULL AND `ElementID` = ". $ID . " AND `Description` = '". $Description ."'")->order('DateAdded','DESC')->all();

            return $data;
        } else {
            return false;
        }
    }

    public static function getOne($ID = NULL, $Description = NULL)
    {
        if($ID) {
            $db   = new DB();
            $data = $db->from(self::$table)->where([self::$elemi => $ID, self::$descr => $Description])->order('DateAdded','DESC')->one();

            return $data;
        } else {
            return false;
        }
    }

    public static function getOneByID($ID = NULL)
    {
        if($ID) {
            $db   = new DB();
            $data = $db->from(self::$table)->where([self::$keyid => $ID])->order('DateAdded','DESC')->one();

            return $data;
        } else {
            return false;
        }
    }

    public static function remove($ID = NULL)
    {
        if($ID) {
            $db   = new DB();
            $data = $db->update(self::$table, ['DateDeleted' => date('Y-m-d H:i:s')], [self::$keyid => $ID]);

            return $data;
        } else {
            return false;
        }
    }
}

Media::init();
