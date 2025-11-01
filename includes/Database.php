<?php
// includes/Database.php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "admissionhub";
    private $conn;
    
    public function __construct() {
        // In future aap .env file use kar sakte hain
        $this->connect();
    }
    
    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection error. Please try again later.");
        }
        
        return $this->conn;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function safeQuery($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>
