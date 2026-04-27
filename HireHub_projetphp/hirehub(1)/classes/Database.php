<?php

class Database {
    public $conn;
    
    public function __construct() {
        // Configuration de la base de données
        $host = 'localhost';
        $dbname = 'hire_hub';
        $username = 'root';
        $password = '';
        
        // Connexion à la base de données
        $this->conn = mysqli_connect($host, $username, $password, $dbname);
        
        // Vérifier la connexion
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        // Définir le charset
        mysqli_set_charset($this->conn, "utf8");
    }
    
    public function __destruct() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
