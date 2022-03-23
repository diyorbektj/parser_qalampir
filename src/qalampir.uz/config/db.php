<?php
require __DIR__.'/../../../vendor/autoload.php';


use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;


class DatabaseService
{

    public static function db()
    {

        $db = [
            'dbname' => "",
            'user' => "",
            'password' => "",
            'host' => "localhost",
            'driver' => "pdo_mysql",
            'charset' => 'utf8mb4'
        ];

        try {
            $conn = DriverManager::getConnection($db, new Configuration());
        } catch (DBALException $e) {
            exit($e);
        }

        return $conn;
    }

}