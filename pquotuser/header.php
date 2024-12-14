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

        <main class="main-content">
            <?php if ($error == '1'): ?>
                <div class="status-card">
                    <div class="institution-info">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2><?php echo $nombre_institucion; ?></h2>
                                <p class="ip-address">IP: <?php echo $ip; ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="detalles.php?ip=<?php echo $ip; ?>" class="inline-flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 font-medium rounded hover:bg-blue-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalles
                                </a>
                                <span id="next-update" class="text-xs text-gray-500 dark:text-gray-400"></span>
                            </div>
                        </div>
                    </div>

                    <div class="quota-info">
                        <div class="quota-text">
                            Usted no cuenta con cuota de internet, pero ha consumido 
                            <span class="highlight"><?php echo $ConsumoUser; ?> <?php echo $unidadConsumoUser; ?></span>
                        </div>

                        <div class="progress-container">
                            <div class="progress-bar" 
                                style="width:<?php echo min($utilizacion, 100); ?>%; 
                                       background-color: <?php echo getColorByPercentage($utilizacion); ?>;
                                       border-radius: 10px;"
                                data-progress="<?php echo round($utilizacion, 1); ?>">
                            </div>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-label">Disponibilidad:</span>
                                <span class="stat-value" id="disponibilidad"><?php echo $disponibilidad; ?></span>
                            </div>

                            <div class="stat-item">
                                <span class="stat-label">Consumo:</span>
                                <span class="stat-value" id="consumo"><?php echo $ConsumoUser; ?> <?php echo $unidadConsumoUser; ?></span>
                            </div>

                            <div class="stat-item" title="Este consumo representa el uso acumulado desde el domingo hasta el día actual de la semana" style="cursor: help;">
                                <span class="stat-label">Consumo Semanal:</span>
                                <span class="stat-value" id="consumo-semanal"><?php echo $used_24h; ?> <?php echo $unidad; ?></span>
                                <div class="tooltip">El consumo se calcula desde el domingo hasta el domingo siguiente</div>
                            </div>
                        </div>

                        <style>
                        .stat-item {
                            position: relative;
                        }
                        
                        .tooltip {
                            visibility: hidden;
                            position: absolute;
                            bottom: 100%;
                            left: 50%;
                            transform: translateX(-50%);
                            background-color: rgba(0, 0, 0, 0.8);
                            color: white;
                            padding: 8px 12px;
                            border-radius: 6px;
                            font-size: 0.9em;
                            white-space: nowrap;
                            z-index: 1000;
                            opacity: 0;
                            transition: opacity 0.3s;
                        }

                        .stat-item:hover .tooltip {
                            visibility: visible;
                            opacity: 1;
                        }

                        .tooltip::after {
                            content: '';
                            position: absolute;
                            top: 100%;
                            left: 50%;
                            margin-left: -5px;
                            border-width: 5px;
                            border-style: solid;
                            border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
                        }
                        </style>

                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error == '2'): ?>
                <div class="status-card">
                    <div class="institution-info">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2><?php echo $nombre_institucion; ?></h2>
                                <p class="ip-address">IP: <?php echo $ip; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="quota-info">
                        <div class="alert alert-danger" style="text-align: center; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                            <div class="alert-content">
                                <i class="alert-icon" style="font-size: 3em; margin-bottom: 15px; display: block;">🚫</i>
                                <h3 style="font-size: 1.5em; margin-bottom: 15px; color: #d32f2f; font-weight: bold;">Servicio No Disponible</h3>
                                <p style="font-size: 1.1em; margin-bottom: 15px; color: #333;">
                                    Lo sentimos, esta dirección IP no tiene acceso al servicio de Internet.
                                </p>
                                <div style="width: 50px; height: 2px; background: #d32f2f; margin: 20px auto;"></div>
                                <p style="font-size: 0.95em; color: #666; line-height: 1.5;">
                                    Si considera que esto es un error, por favor contacte al<br>
                                    administrador del sistema para obtener asistencia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error == ''): ?>
                <div class="status-card">
                    <div class="institution-info">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2><?php echo $nombre_institucion; ?></h2>
                                <p class="ip-address">IP: <?php echo $ip; ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="detalles.php?ip=<?php echo $ip; ?>" class="inline-flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 font-medium rounded hover:bg-blue-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalles
                                </a>
                                <span id="next-update" class="text-xs text-gray-500 dark:text-gray-400"></span>
                            </div>
                        </div>
                    </div>

                    <div class="quota-info">
                        <div class="quota-text">
                            Usted ha consumido el 
                            <span class="percentage" id="utilizacion"><?php echo round($utilizacion, 1); ?>%</span> 
                            de su cuota asignada de 
                            <span class="quota" id="cuota-asignada"><?php echo $CuotaAsignada; ?> <?php echo $unidadCuotaAsignada; ?></span>
                        </div>

                        <!-- Reemplaza la sección de la barra de progreso con esto -->
                        <div class="progress-container">
                            <div class="progress-bar" 
                                style="width:<?php echo min($utilizacion, 100); ?>%; 
                                       background-color: <?php echo getColorByPercentage($utilizacion); ?>;
                                       border-radius: 10px;"
                                data-progress="<?php echo round($utilizacion, 1); ?>">
                            </div>
                        </div>


                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-label">Disponibilidad:</span>
                                <span class="stat-value" id="disponibilidad"><?php echo $disponibilidad; ?></span>
                            </div>

                            <div class="stat-item">
                                <span class="stat-label">Consumo:</span>
                                <span class="stat-value" id="consumo"><?php echo $ConsumoUser; ?> <?php echo $unidadConsumoUser; ?></span>
                            </div>

                            <div class="stat-item" title="Este consumo representa el uso acumulado desde el domingo hasta el día actual de la semana y se reinicia cada domingo" style="cursor: help;">
                                <span class="stat-label">Consumo Semanal:</span>
                                <span class="stat-value" id="consumo-semanal"><?php echo $used_24h; ?> <?php echo $unidad; ?></span>
                                <div class="tooltip">El consumo se calcula desde el domingo hasta el domingo siguiente y se reinicia cada domingo</div>
                            </div>
                        </div>

                        <style>
                        .stat-item {
                            position: relative;
                        }
                        
                        .tooltip {
                            visibility: hidden;
                            position: absolute;
                            bottom: 100%;
                            left: 50%;
                            transform: translateX(-50%);
                            background-color: rgba(0, 0, 0, 0.8);
                            color: white;
                            padding: 8px 12px;
                            border-radius: 6px;
                            font-size: 0.9em;
                            white-space: nowrap;
                            z-index: 1000;
                            opacity: 0;
                            transition: opacity 0.3s;
                        }

                        .stat-item:hover .tooltip {
                            visibility: visible;
                            opacity: 1;a
                        }

                        .tooltip::after {
                            content: '';
                            position: absolute;
                            top: 100%;
                            left: 50%;
                            margin-left: -5px;
                            border-width: 5px;
                            border-style: solid;
                            border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
                        }
                        </style>

                       
                    </div>
                </div>
            <?php endif; ?>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                function blendColors(color1, color2, ratio) {
                    return color1.map((c, i) => Math.round(c + ratio * (color2[i] - c)));
                }

                function getColorByPercentage(percentage) {
                    const colorStart = [76, 175, 80]; // Verde claro
                    const colorMiddle = [255, 235, 59]; // Amarillo claro
                    const colorEnd = [244, 67, 54]; // Rojo oscuro

                    let color;
                    if (percentage >= 100) {
                        color = colorEnd;
                    } else if (percentage <= 50) {
                        const ratio = percentage / 50;
                        color = blendColors(colorStart, colorMiddle, ratio);
                    } else {
                        const ratio = (percentage - 50) / 50;
                        color = blendColors(colorMiddle, colorEnd, ratio);
                    }

                    return `rgb(${color.join(",")})`;
                }

                function updateProgress() {
                    fetch('get_data.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.error === '') {
                                document.getElementById('utilizacion').textContent = data.utilizacion + '%';
                                document.getElementById('cuota-asignada').textContent = data.CuotaAsignada;
                                document.getElementById('disponibilidad').textContent = data.disponibilidad;
                                document.getElementById('consumo').textContent = data.ConsumoUser;
                                document.getElementById('consumo-semanal').textContent = data.used_24h;

                                const progressBar = document.querySelector('.progress-bar');
                                progressBar.style.width = Math.min(data.utilizacion, 100) + '%';
                                progressBar.style.backgroundColor = getColorByPercentage(data.utilizacion);
                            }
                        })
                        .catch(error => console.error('Error al obtener los datos:', error));
                }

                // Actualizar cada 5 segundos
                setInterval(updateProgress, 5000);
                updateProgress(); // Llamar inmediatamente para la primera actualización
            });
            </script>




