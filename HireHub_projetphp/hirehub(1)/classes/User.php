<?php

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Enregistrer un nouvel utilisateur
     */
    public function register($name, $email, $password) {
        // Vérifier si l'email existe déjà
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($this->db->conn, $checkSql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            return false; // Email déjà utilisé
        }
        
        // Vérifier si la colonne 'name' existe
        $hasNameColumn = false;
        $columns = mysqli_query($this->db->conn, "SHOW COLUMNS FROM users LIKE 'name'");
        if (mysqli_num_rows($columns) > 0) {
            $hasNameColumn = true;
        }
        
        // Insérer le nouvel utilisateur (mot de passe en clair)
        if ($hasNameColumn) {
            $sql = "INSERT INTO users (email, password, role, name) VALUES (?, ?, 'ROLE_USER', ?)";
            $stmt = mysqli_prepare($this->db->conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $email, $password, $name);
        } else {
            // Si la colonne name n'existe pas, on l'ajoute d'abord
            mysqli_query($this->db->conn, "ALTER TABLE users ADD COLUMN name VARCHAR(255) AFTER email");
            $sql = "INSERT INTO users (email, password, role, name) VALUES (?, ?, 'ROLE_USER', ?)";
            $stmt = mysqli_prepare($this->db->conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $email, $password, $name);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Connecter un utilisateur
     */
    public function login($email, $password) {
        // Vérifier si la colonne 'name' existe
        $hasNameColumn = false;
        $columns = mysqli_query($this->db->conn, "SHOW COLUMNS FROM users LIKE 'name'");
        if (mysqli_num_rows($columns) > 0) {
            $hasNameColumn = true;
        }
        
        // Récupérer l'utilisateur par email
        if ($hasNameColumn) {
            $sql = "SELECT id, email, password, role, name FROM users WHERE email = ?";
        } else {
            $sql = "SELECT id, email, password, role FROM users WHERE email = ?";
        }
        
        $stmt = mysqli_prepare($this->db->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Vérifier le mot de passe en clair
            if ($user['password'] === $password) {
                // Créer la session utilisateur
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'] ?? $user['email'], // Utiliser email si name n'existe pas
                    'role' => $this->mapRole($user['role']) // Convertir ROLE_USER -> USER, ROLE_ADMIN -> ADMIN
                ];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Mapper les rôles de la base de données vers les rôles utilisés dans l'application
     */
    private function mapRole($dbRole) {
        if ($dbRole === 'ROLE_ADMIN' || $dbRole === 'ADMIN') {
            return 'ADMIN';
        }
        return 'USER';
    }
}
