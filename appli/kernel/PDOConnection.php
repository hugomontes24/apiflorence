<?php

class PDOConnection
{
    private static ?\PDO $pdo = null;


    public static function get(): PDO
    {
        if(is_null(self::$pdo)){
            self::$pdo = self::createPdoInstance();
        }
        return self::$pdo;
    }


    private static function createPdoInstance(): PDO
    {

        try{
            $oPdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_BASE.";charset=utf8","root","root" );
            $oPdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if(defined('DB_SQL_DEBUG')){
                $oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return $oPdo;
        } catch(PDOException $exception){
            throw new Exception($exception->getMessage(), 99);
        }
        return $oPdo;
    }
}