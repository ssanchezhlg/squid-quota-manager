<?php
require_once('validaciones.php');
require_once 'connect.php';

$conexion = conexion();
$ip = isset($_GET['ip']) ? $_GET['ip'] : '';

// Validar IP
if (!validaIp($ip)) {
    header('Location: index.php');
    exit;
}

// Obtener datos del usuario
$query = "SELECT * FROM " . TABLE_NAME . " WHERE " . CLIENTE_IP . "='" . $conexion->real_escape_string($ip) . "'";
$result = $conexion->query($query);

if (!$result || $result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$userData = $result->fetch_assoc();

// Función para determinar la unidad y convertir el valor
function formatearConsumo($bytes) {
    $gb = 1024 * 1024 * 1024;
    $mb = 1024 * 1024;
    $kb = 1024;

    if ($bytes >= $gb) {
        return [round($bytes / $gb, 2), 'GB'];
    } elseif ($bytes >= $mb) {
        return [round($bytes / $mb, 2), 'MB'];
    } else {
        return [round($bytes / $kb, 2), 'KB'];
    }
}

// Formatear los tres consumos directamente desde los bytes
list($used_24h, $unidad_24h) = formatearConsumo($userData[USED_QUOTA_24H]);
list($used_mensual, $unidad_mensual) = formatearConsumo($userData['used_quota_mensual']);
list($used_anual, $unidad_anual) = formatearConsumo($userData['used_quota_anual']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de cuota de Infomed - Holguín</title>
    <link rel="stylesheet" href="assets/css/css2.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/tailwindcss.js"></script>
</head>
<body>
    <div class="page-wrapper">
        <header class="main-header">
            <div class="header-container">
                <div class="logo-container">
                    <img src="assets/images/logoinfomed.png" alt="Logo Infomed" class="logo-image">
                </div>
                
                <div class="header-title">
                    <h1>Sistema de Cuotas</h1>
                    <p class="subtitle">Infomed Holguín</p>
                </div>
            </div>
        </header>

        <!-- Inicio del contenido principal -->
        <main class="main-content">
            <div class="status-card">
                <div class="institution-info">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2><?php echo htmlspecialchars($userData[ORGANIZATION]); ?></h2>
                            <p class="ip-address">IP: <?php echo htmlspecialchars($ip); ?></p>
                        </div>
                    </div>
                </div>

                <div class="details-content">
                    <div class="stats-grid grid grid-cols-3 gap-4">
                        <div class="stat-item">
                            <span class="stat-label">Consumo Semanal:</span>
                            <span class="stat-value"><?php echo $used_24h; ?> <?php echo $unidad_24h; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Consumo Mensual:</span>
                            <span class="stat-value"><?php echo $used_mensual; ?> <?php echo $unidad_mensual; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Consumo Anual:</span>
                            <span class="stat-value"><?php echo $used_anual; ?> <?php echo $unidad_anual; ?></span>
                        </div>
                    </div>
                </div>

                <div class="back-button-container mt-6">
                    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </main>

        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
