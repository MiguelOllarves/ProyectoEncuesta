<?php
function getConexion(): PDO
{
    $uri = getenv('DATABASE_URL');
    if (!$uri) {
        throw new Exception('DATABASE_URL no está configurada.');
    }

    $parts = parse_url($uri);
    if ($parts === false || !isset($parts['scheme'], $parts['host'], $parts['user'], $parts['pass'], $parts['path'])) {
        throw new Exception('DATABASE_URL inválida.');
    }

    $host = $parts['host'];
    $port = $parts['port'] ?? '5432';
    $dbname = ltrim($parts['path'], '/');
    $user = $parts['user'];
    $password = $parts['pass'];

    $query = [];
    parse_str($parts['query'] ?? '', $query);
    $sslmode = $query['sslmode'] ?? 'require';

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s;sslmode=%s',
        $host,
        $port,
        $dbname,
        $user,
        $password,
        $sslmode
    );

    if (isset($query['channel_binding'])) {
        $dsn .= ';channel_binding=' . $query['channel_binding'];
    }

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('Error de conexión a la base de datos: ' . $e->getMessage());
    }
}