<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSON inválido o no recibido']);
    exit;
}

try {
    $conn = getConexion();

    $sql = "INSERT INTO reportes_cuadrantes (
                cuadrante, escuadra, sector_ubicacion,
                ofi_gral, ofi_sup, ofi_sub, tt_pp, tt_aa,
                proteccion_civil, bomberos, delegacion_extr,
                evacuados_con_vida, evacuados_sin_vida,
                retroexcavadora, jumbo, camiones_volteo, plantas_electricas,
                edificaciones_intervenidas, edificios_inspeccionados,
                viviendas_casas, locales_comerciales, escuelas,
                edificaciones_colapsadas, edificaciones_para_demoler,
                edificaciones_recuperables, logros_alcanzados
            ) VALUES (
                :cuadrante, :escuadra, :sector_ubicacion,
                :ofi_gral, :ofi_sup, :ofi_sub, :tt_pp, :tt_aa,
                :proteccion_civil, :bomberos, :delegacion_extr,
                :evacuados_con_vida, :evacuados_sin_vida,
                :retroexcavadora, :jumbo, :camiones_volteo, :plantas_electricas,
                :edificaciones_intervenidas, :edificios_inspeccionados,
                :viviendas_casas, :locales_comerciales, :escuelas,
                :edificaciones_colapsadas, :edificaciones_para_demoler,
                :edificaciones_recuperables, :logros_alcanzados
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':cuadrante' => (int)($data['cuadrante'] ?? 0),
        ':escuadra' => (int)($data['escuadra'] ?? 0),
        ':sector_ubicacion' => trim((string)($data['sector_ubicacion'] ?? '')),
        ':ofi_gral' => (int)($data['ofi_gral'] ?? 0),
        ':ofi_sup' => (int)($data['ofi_sup'] ?? 0),
        ':ofi_sub' => (int)($data['ofi_sub'] ?? 0),
        ':tt_pp' => (int)($data['tt_pp'] ?? 0),
        ':tt_aa' => (int)($data['tt_aa'] ?? 0),
        ':proteccion_civil' => (int)($data['proteccion_civil'] ?? 0),
        ':bomberos' => (int)($data['bomberos'] ?? 0),
        ':delegacion_extr' => (int)($data['delegacion_extr'] ?? 0),
        ':evacuados_con_vida' => (int)($data['evacuados_con_vida'] ?? 0),
        ':evacuados_sin_vida' => (int)($data['evacuados_sin_vida'] ?? 0),
        ':retroexcavadora' => (int)($data['retroexcavadora'] ?? 0),
        ':jumbo' => (int)($data['jumbo'] ?? 0),
        ':camiones_volteo' => (int)($data['camiones_volteo'] ?? 0),
        ':plantas_electricas' => (int)($data['plantas_electricas'] ?? 0),
        ':edificaciones_intervenidas' => (int)($data['edificaciones_intervenidas'] ?? 0),
        ':edificios_inspeccionados' => (int)($data['edificios_inspeccionados'] ?? 0),
        ':viviendas_casas' => (int)($data['viviendas_casas'] ?? 0),
        ':locales_comerciales' => (int)($data['locales_comerciales'] ?? 0),
        ':escuelas' => (int)($data['escuelas'] ?? 0),
        ':edificaciones_colapsadas' => (int)($data['edificaciones_colapsadas'] ?? 0),
        ':edificaciones_para_demoler' => (int)($data['edificaciones_para_demoler'] ?? 0),
        ':edificaciones_recuperables' => (int)($data['edificaciones_recuperables'] ?? 0),
        ':logros_alcanzados' => trim((string)($data['logros_alcanzados'] ?? ''))
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}