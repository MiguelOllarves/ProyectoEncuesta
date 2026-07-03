<?php
$uri = getenv('DATABASE_URL') ?: "";
if (empty($uri)) die("DATABASE_URL no configurada.\n");
$db = parse_url($uri);
$dsn = "pgsql:host=" . $db["host"] . ";port=" . $db["port"] . ";dbname=" . ltrim($db["path"], "/");

try {
    $conn = new PDO($dsn, $db["user"], $db["pass"]);
    echo "¡CONEXIÓN EXITOSA CON neon!";
} catch (PDOException $e) {
    echo "ERROR DE CONEXIÓN: " . $e->getMessage();
}
?>