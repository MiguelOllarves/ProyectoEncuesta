<?php
/**
 * Conexión segura a la base de datos Neon (PostgreSQL) usando PDO
 * Aplicando OWASP mitigations para prepared statements.
 */
class Database {
    // String provided by the user
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;

    public function __construct() {
        $uri = getenv('DATABASE_URL');
        if($uri) {
            $db = parse_url($uri);
            $this->host = $db["host"] ?? "";
            $this->port = $db["port"] ?? "5432";
            $this->db_name = ltrim($db["path"] ?? "", "/");
            $this->username = $db["user"] ?? "";
            $this->password = $db["pass"] ?? "";
        }
    }
    
    public $conn;

    /**
     * Retorna y establece la conexión PDO de manera segura
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // DSN for PostgresSQL connection securely enforcing SSL
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Set error mode to exception to prevent exposed raw errors
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // By default emulate prepares off, use real prepared statements
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Default fetch mode associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            // In a real env, log this error in a file instead of displaying
            error_log("Connection error: " . $exception->getMessage());
            die(json_encode(["success" => false, "message" => "Ocurrió un error al contactar la base de datos."]));
        }

        return $this->conn;
    }
}
?>
