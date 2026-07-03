<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/database.php';

// Aceptar unicamente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metodo no permitido"]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // 1. Recibir Input Raw (JSON desde Fetch)
    $data = json_decode(file_get_contents("php://input"), true);
    if(!$data) {
        // Fallback a POST form normal
        $data = $_POST;
    }
    
    // 2. Sanitizacion Básica y Validacion (OWASP Mitigation)
    $cuadrante = filter_var($data['cuadrante'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $escuadra = filter_var($data['escuadra'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $logros = filter_var($data['logros'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $novedades = filter_var($data['novedades'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    
    if (empty($cuadrante) || empty($escuadra)) {
        throw new Exception("Cuadrante y Escuadra son requeridos");
    }

    // 3. Preparar Consulta Inserción Parametrizada
    $query = "INSERT INTO operaciones (cuadrante, escuadra, logros, novedades, estado, fecha) 
              VALUES (:c, :e, :l, :n, 'Activo', CURRENT_TIMESTAMP)";
              
    $stmt = $db->prepare($query);
    
    // 4. Executar transaccion segura
    $stmt->execute([
        ':c' => $cuadrante,
        ':e' => $escuadra,
        ':l' => $logros,
        ':n' => $novedades
    ]);
    
    echo json_encode([
        "success" => true,
        "message" => "Operación registrada exitosamente."
    ]);

} catch (Exception $e) {
    http_response_code(400);
    // Para entornos en vivo no devolver $e->getMessage() crudo si expone schema.
    echo json_encode(["success" => false, "message" => "Error al guardar: " . $e->getMessage()]);
}
?>
