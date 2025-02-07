<?php

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $password;

    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
    }
    public function getConnection() :PDO
    {
       $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8"; 
       return new PDO($dsn, $this->user, $this->password, [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
       ]);
    }

    
        
}