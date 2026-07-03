<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

function columnExists(PDO $conn, string $table, string $column): bool
{
    $stmt = $conn->prepare('SELECT 1 FROM information_schema.columns WHERE table_name = :table AND column_name = :column');
    $stmt->execute([':table' => $table, ':column' => $column]);
    return (bool) $stmt->fetchColumn();
}

function fetchTimeline(PDO $conn, string $whereSql, array $params, string $interval): array
{
    $sql = sprintf(
        "SELECT DATE_TRUNC('%s', fecha_registro) AS bucket, COUNT(*) AS total FROM reportes_cuadrantes WHERE %s GROUP BY 1 ORDER BY 1",
        $interval,
        $whereSql
    );
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(static function ($row) {
        return [
            'label' => substr((string) $row['bucket'], 0, 10),
            'value' => (int) $row['total']
        ];
    }, $rows);
}

try {
    $conn = getConexion();

    $hasUsuario = columnExists($conn, 'reportes_cuadrantes', 'usuario_registro');
    $hasEstado = columnExists($conn, 'reportes_cuadrantes', 'estado_reporte');
    $hasMunicipio = columnExists($conn, 'reportes_cuadrantes', 'municipio');
    $hasParroquia = columnExists($conn, 'reportes_cuadrantes', 'parroquia');

    $conditions = ['1 = 1'];
    $params = [];

    if (!empty($_GET['fecha_desde'])) {
        $conditions[] = 'CAST(fecha_registro AS DATE) >= :fecha_desde';
        $params[':fecha_desde'] = trim((string) $_GET['fecha_desde']);
    }

    if (!empty($_GET['fecha_hasta'])) {
        $conditions[] = 'CAST(fecha_registro AS DATE) <= :fecha_hasta';
        $params[':fecha_hasta'] = trim((string) $_GET['fecha_hasta']);
    }

    if (!empty($_GET['cuadrante'])) {
        $conditions[] = 'cuadrante = :cuadrante';
        $params[':cuadrante'] = (int) $_GET['cuadrante'];
    }

    if (!empty($_GET['escuadra'])) {
        $conditions[] = 'escuadra = :escuadra';
        $params[':escuadra'] = (int) $_GET['escuadra'];
    }

    if ($hasUsuario && !empty($_GET['usuario'])) {
        $conditions[] = 'usuario_registro = :usuario';
        $params[':usuario'] = trim((string) $_GET['usuario']);
    }

    if ($hasEstado && !empty($_GET['estado'])) {
        $conditions[] = 'estado_reporte = :estado';
        $params[':estado'] = trim((string) $_GET['estado']);
    }

    if ($hasMunicipio && !empty($_GET['municipio'])) {
        $conditions[] = 'municipio = :municipio';
        $params[':municipio'] = trim((string) $_GET['municipio']);
    }

    if ($hasParroquia && !empty($_GET['parroquia'])) {
        $conditions[] = 'parroquia = :parroquia';
        $params[':parroquia'] = trim((string) $_GET['parroquia']);
    }

    $whereSql = implode(' AND ', $conditions);

    $summarySql = "
        SELECT
            COUNT(*) AS total_reportes,
            COUNT(DISTINCT cuadrante) AS total_cuadrantes,
            COUNT(DISTINCT escuadra) AS total_escuadras,
            COALESCE(SUM(ofi_gral), 0) AS total_ofi_gral,
            COALESCE(SUM(ofi_sup), 0) AS total_ofi_sup,
            COALESCE(SUM(ofi_sub), 0) AS total_ofi_sub,
            COALESCE(SUM(tt_pp), 0) AS total_tt_pp,
            COALESCE(SUM(tt_aa), 0) AS total_tt_aa,
            COALESCE(SUM(proteccion_civil), 0) AS total_proteccion_civil,
            COALESCE(SUM(bomberos), 0) AS total_bomberos,
            COALESCE(SUM(delegacion_extr), 0) AS total_delegacion_extr,
            COALESCE(SUM(evacuados_con_vida), 0) AS total_evacuados_vida,
            COALESCE(SUM(evacuados_sin_vida), 0) AS total_evacuados_sin_vida,
            COALESCE(SUM(retroexcavadora), 0) AS total_retroexcavadora,
            COALESCE(SUM(jumbo), 0) AS total_jumbo,
            COALESCE(SUM(camiones_volteo), 0) AS total_camiones_volteo,
            COALESCE(SUM(plantas_electricas), 0) AS total_plantas_electricas,
            COALESCE(SUM(edificaciones_intervenidas), 0) AS total_edificaciones_intervenidas,
            COALESCE(SUM(edificios_inspeccionados), 0) AS total_edificios_inspeccionados,
            COALESCE(SUM(viviendas_casas), 0) AS total_viviendas_casas,
            COALESCE(SUM(locales_comerciales), 0) AS total_locales_comerciales,
            COALESCE(SUM(escuelas), 0) AS total_escuelas,
            COALESCE(SUM(edificaciones_colapsadas), 0) AS total_edificaciones_colapsadas,
            COALESCE(SUM(edificaciones_para_demoler), 0) AS total_edificaciones_para_demoler,
            COALESCE(SUM(edificaciones_recuperables), 0) AS total_edificaciones_recuperables,
            COUNT(CASE WHEN TRIM(COALESCE(logros_alcanzados, '')) <> '' THEN 1 END) AS total_logros,
            COUNT(CASE WHEN TRIM(COALESCE(logros_alcanzados, '')) = '' THEN 1 END) AS total_novedades
        FROM reportes_cuadrantes
        WHERE {$whereSql}
    ";

    $stmtSummary = $conn->prepare($summarySql);
    $stmtSummary->execute($params);
    $summary = $stmtSummary->fetch(PDO::FETCH_ASSOC) ?: [];

    $totalReportes = max(1, (int) ($summary['total_reportes'] ?? 0));
    $totalPersonal = max(1, (int) ($summary['total_ofi_gral'] ?? 0) + (int) ($summary['total_ofi_sup'] ?? 0) + (int) ($summary['total_ofi_sub'] ?? 0) + (int) ($summary['total_tt_pp'] ?? 0) + (int) ($summary['total_tt_aa'] ?? 0));
    $totalMaquinaria = max(1, (int) ($summary['total_retroexcavadora'] ?? 0) + (int) ($summary['total_jumbo'] ?? 0) + (int) ($summary['total_camiones_volteo'] ?? 0) + (int) ($summary['total_plantas_electricas'] ?? 0));
    $totalEdificaciones = max(1, (int) ($summary['total_edificaciones_intervenidas'] ?? 0) + (int) ($summary['total_edificios_inspeccionados'] ?? 0) + (int) ($summary['total_viviendas_casas'] ?? 0) + (int) ($summary['total_locales_comerciales'] ?? 0) + (int) ($summary['total_escuelas'] ?? 0) + (int) ($summary['total_edificaciones_colapsadas'] ?? 0) + (int) ($summary['total_edificaciones_para_demoler'] ?? 0) + (int) ($summary['total_edificaciones_recuperables'] ?? 0));

    $personnelCards = [
        ['label' => 'OFI/GRAL', 'value' => (int) ($summary['total_ofi_gral'] ?? 0), 'percent' => round(((int) ($summary['total_ofi_gral'] ?? 0) / $totalPersonal) * 100, 1)],
        ['label' => 'OFI/SUP', 'value' => (int) ($summary['total_ofi_sup'] ?? 0), 'percent' => round(((int) ($summary['total_ofi_sup'] ?? 0) / $totalPersonal) * 100, 1)],
        ['label' => 'OFI/SUB', 'value' => (int) ($summary['total_ofi_sub'] ?? 0), 'percent' => round(((int) ($summary['total_ofi_sub'] ?? 0) / $totalPersonal) * 100, 1)],
        ['label' => 'TT/PP', 'value' => (int) ($summary['total_tt_pp'] ?? 0), 'percent' => round(((int) ($summary['total_tt_pp'] ?? 0) / $totalPersonal) * 100, 1)],
        ['label' => 'TT/AA', 'value' => (int) ($summary['total_tt_aa'] ?? 0), 'percent' => round(((int) ($summary['total_tt_aa'] ?? 0) / $totalPersonal) * 100, 1)],
        ['label' => 'PROTECCIÓN CIVIL', 'value' => (int) ($summary['total_proteccion_civil'] ?? 0), 'percent' => round(((int) ($summary['total_proteccion_civil'] ?? 0) / max(1, (int) ($summary['total_proteccion_civil'] ?? 0) + (int) ($summary['total_bomberos'] ?? 0) + (int) ($summary['total_delegacion_extr'] ?? 0))) * 100, 1)],
        ['label' => 'BOMBEROS', 'value' => (int) ($summary['total_bomberos'] ?? 0), 'percent' => round(((int) ($summary['total_bomberos'] ?? 0) / max(1, (int) ($summary['total_proteccion_civil'] ?? 0) + (int) ($summary['total_bomberos'] ?? 0) + (int) ($summary['total_delegacion_extr'] ?? 0))) * 100, 1)],
        ['label' => 'DELEGACIONES EXTRANJERAS', 'value' => (int) ($summary['total_delegacion_extr'] ?? 0), 'percent' => round(((int) ($summary['total_delegacion_extr'] ?? 0) / max(1, (int) ($summary['total_proteccion_civil'] ?? 0) + (int) ($summary['total_bomberos'] ?? 0) + (int) ($summary['total_delegacion_extr'] ?? 0))) * 100, 1)]
    ];

    $evacuationCards = [
        ['label' => 'Con vida', 'value' => (int) ($summary['total_evacuados_vida'] ?? 0), 'percent' => round(((int) ($summary['total_evacuados_vida'] ?? 0) / max(1, (int) ($summary['total_evacuados_vida'] ?? 0) + (int) ($summary['total_evacuados_sin_vida'] ?? 0))) * 100, 1)],
        ['label' => 'Sin vida', 'value' => (int) ($summary['total_evacuados_sin_vida'] ?? 0), 'percent' => round(((int) ($summary['total_evacuados_sin_vida'] ?? 0) / max(1, (int) ($summary['total_evacuados_vida'] ?? 0) + (int) ($summary['total_evacuados_sin_vida'] ?? 0))) * 100, 1)]
    ];

    $machineryCards = [
        ['label' => 'Retroexcavadoras', 'value' => (int) ($summary['total_retroexcavadora'] ?? 0), 'percent' => round(((int) ($summary['total_retroexcavadora'] ?? 0) / $totalMaquinaria) * 100, 1)],
        ['label' => 'Jumbos', 'value' => (int) ($summary['total_jumbo'] ?? 0), 'percent' => round(((int) ($summary['total_jumbo'] ?? 0) / $totalMaquinaria) * 100, 1)],
        ['label' => 'Camiones volteo', 'value' => (int) ($summary['total_camiones_volteo'] ?? 0), 'percent' => round(((int) ($summary['total_camiones_volteo'] ?? 0) / $totalMaquinaria) * 100, 1)],
        ['label' => 'Plantas eléctricas', 'value' => (int) ($summary['total_plantas_electricas'] ?? 0), 'percent' => round(((int) ($summary['total_plantas_electricas'] ?? 0) / $totalMaquinaria) * 100, 1)]
    ];

    $buildingCards = [
        ['label' => 'Intervenidas', 'value' => (int) ($summary['total_edificaciones_intervenidas'] ?? 0), 'percent' => round(((int) ($summary['total_edificaciones_intervenidas'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Inspeccionadas', 'value' => (int) ($summary['total_edificios_inspeccionados'] ?? 0), 'percent' => round(((int) ($summary['total_edificios_inspeccionados'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Viviendas', 'value' => (int) ($summary['total_viviendas_casas'] ?? 0), 'percent' => round(((int) ($summary['total_viviendas_casas'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Locales comerciales', 'value' => (int) ($summary['total_locales_comerciales'] ?? 0), 'percent' => round(((int) ($summary['total_locales_comerciales'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Escuelas', 'value' => (int) ($summary['total_escuelas'] ?? 0), 'percent' => round(((int) ($summary['total_escuelas'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Colapsadas', 'value' => (int) ($summary['total_edificaciones_colapsadas'] ?? 0), 'percent' => round(((int) ($summary['total_edificaciones_colapsadas'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Para demoler', 'value' => (int) ($summary['total_edificaciones_para_demoler'] ?? 0), 'percent' => round(((int) ($summary['total_edificaciones_para_demoler'] ?? 0) / $totalEdificaciones) * 100, 1)],
        ['label' => 'Recuperables', 'value' => (int) ($summary['total_edificaciones_recuperables'] ?? 0), 'percent' => round(((int) ($summary['total_edificaciones_recuperables'] ?? 0) / $totalEdificaciones) * 100, 1)]
    ];

    $latestSql = "
        SELECT cuadrante, escuadra, sector_ubicacion, logros_alcanzados, fecha_registro
        FROM reportes_cuadrantes
        WHERE {$whereSql}
        ORDER BY fecha_registro DESC
        LIMIT 8
    ";
    $stmtLatest = $conn->prepare($latestSql);
    $stmtLatest->execute($params);
    $latest = $stmtLatest->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'totales' => [
                'total_reportes' => (int) ($summary['total_reportes'] ?? 0),
                'total_cuadrantes' => (int) ($summary['total_cuadrantes'] ?? 0),
                'total_escuadras' => (int) ($summary['total_escuadras'] ?? 0),
                'total_efectivos' => (int) ($summary['total_ofi_gral'] ?? 0) + (int) ($summary['total_ofi_sup'] ?? 0) + (int) ($summary['total_ofi_sub'] ?? 0) + (int) ($summary['total_tt_pp'] ?? 0) + (int) ($summary['total_tt_aa'] ?? 0),
                'total_evacuados_vida' => (int) ($summary['total_evacuados_vida'] ?? 0),
                'total_evacuados_sin_vida' => (int) ($summary['total_evacuados_sin_vida'] ?? 0),
                'total_maquinaria' => (int) ($summary['total_retroexcavadora'] ?? 0) + (int) ($summary['total_jumbo'] ?? 0) + (int) ($summary['total_camiones_volteo'] ?? 0) + (int) ($summary['total_plantas_electricas'] ?? 0),
                'total_edificaciones' => (int) ($summary['total_edificaciones_intervenidas'] ?? 0) + (int) ($summary['total_edificios_inspeccionados'] ?? 0)
            ],
            'personnel_cards' => $personnelCards,
            'evacuation_cards' => $evacuationCards,
            'machinery_cards' => $machineryCards,
            'building_cards' => $buildingCards,
            'operations' => [
                'logros' => (int) ($summary['total_logros'] ?? 0),
                'novedades' => (int) ($summary['total_novedades'] ?? 0)
            ],
            'series' => [
                'daily' => fetchTimeline($conn, $whereSql, $params, 'day'),
                'weekly' => fetchTimeline($conn, $whereSql, $params, 'week'),
                'monthly' => fetchTimeline($conn, $whereSql, $params, 'month')
            ],
            'actividad_reciente' => $latest,
            'filters' => [
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
                'cuadrante' => $_GET['cuadrante'] ?? '',
                'escuadra' => $_GET['escuadra'] ?? '',
                'usuario' => $_GET['usuario'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'municipio' => $_GET['municipio'] ?? '',
                'parroquia' => $_GET['parroquia'] ?? ''
            ]
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
