<?php
require_once('connect.php');
require_once('constants.php');

// Obtener el IP del cliente
$ip = $_SERVER['REMOTE_ADDR'];

// Crear conexión
$conn = conexion();

// Consulta SQL usando las constantes
$sql = "SELECT * FROM " . TABLE_NAME . " WHERE " . CLIENTE_IP . " = '$ip'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Cálculos
    $quota = $row[QUOTA];
    $used = $row[USED];
    $used_24h = $row[USED_QUOTA_24H];
    
    // Convertir a unidades legibles
    $CuotaAsignada = formatBytes($quota);
    $ConsumoUser = formatBytes($used);
    $used_24h_formatted = formatBytes($used_24h);
    
    // Calcular utilización y disponibilidad
    $utilizacion = ($quota > 0) ? ($used / $quota) * 100 : 0;
    $disponibilidad = formatBytes(max(0, $quota - $used));
    
    // Preparar respuesta
    $response = [
        'utilizacion' => round($utilizacion, 1),
        'CuotaAsignada' => $CuotaAsignada,
        'disponibilidad' => $disponibilidad,
        'ConsumoUser' => $ConsumoUser,
        'used_24h' => $used_24h_formatted,
        'error' => ''
    ];
} else {
    $response = [
        'error' => '2' // No tiene servicio de Internet
    ];
}

$conn->close();

// Función para formatear bytes (si no la tienes ya definida)
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Devolver JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
