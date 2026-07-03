<?php
// setup.php - Ejecutar una sola vez y luego borrar
$uri = getenv('DATABASE_URL') ?: ""; 
if (empty($uri)) die("DATABASE_URL no está configurada.");
$db = parse_url($uri);

// Extraer el sslmode de la URL si existe
parse_str($db['query'] ?? '', $query_params);
$sslmode = isset($query_params['sslmode']) ? "sslmode=" . $query_params['sslmode'] : "";

$conn_string = sprintf(
    "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s;%s",
    $db["host"],
    $db["port"],
    ltrim($db["path"], "/"),
    $db["user"],
    $db["pass"],
    $sslmode
);

try {
    $conn = new PDO($conn_string);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
    CREATE TABLE IF NOT EXISTS reportes_cuadrantes (
        id SERIAL PRIMARY KEY,
        cuadrante INT NOT NULL,
        escuadra INT NOT NULL,
        sector_ubicacion VARCHAR(255) NOT NULL,
        ofi_gral INT DEFAULT 0,
        ofi_sup INT DEFAULT 0,
        ofi_sub INT DEFAULT 0,
        tt_pp INT DEFAULT 0,
        tt_aa INT DEFAULT 0,
        proteccion_civil INT DEFAULT 0,
        bomberos INT DEFAULT 0,
        delegacion_extr INT DEFAULT 0,
        evacuados_con_vida INT DEFAULT 0,
        evacuados_sin_vida INT DEFAULT 0,
        retroexcavadora INT DEFAULT 0,
        jumbo INT DEFAULT 0,
        camiones_volteo INT DEFAULT 0,
        plantas_electricas INT DEFAULT 0,
        edificaciones_intervenidas INT DEFAULT 0,
        edificios_inspeccionados INT DEFAULT 0,
        viviendas_casas INT DEFAULT 0,
        locales_comerciales INT DEFAULT 0,
        escuelas INT DEFAULT 0,
        edificaciones_colapsadas INT DEFAULT 0,
        edificaciones_para_demoler INT DEFAULT 0,
        edificaciones_recuperables INT DEFAULT 0,
        logros_alcanzados TEXT,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $conn->exec($sql);
    echo "<h1>¡Éxito!</h1><p>Las tablas se crearon correctamente en Aiven.</p>";
} catch (PDOException $e) {
    echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
}
?>