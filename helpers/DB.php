<?php

namespace helpers;

use PDO;
use PDOException;

class DB {

    private const DB_HOST = 'hostname_placeholder';
    private const DB_NAME = 'studentregistration';
    private const DB_USER = 'root';
    private const DB_PASS = 'Wayne123!';

    private static $instance;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new \PDO("mysql:host=".self::DB_HOST.";dbname=".self::DB_NAME, self::DB_USER, self::DB_PASS);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            // Log or handle the exception, avoid echoing in a helper class
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): DB {
        if(!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO {
        return $this->conn;
    }
}
