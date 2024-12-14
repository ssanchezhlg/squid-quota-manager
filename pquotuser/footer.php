<footer class="main-footer">
            <div class="footer-content">
                <div class="info-section">
                    <p class="info-text">
                        Con el objetivo de optimizar el uso del canal de internet y garantizar la calidad de los servicios de Infomed, se ha implementado el sistema de cuotas.
                    </p>
                    
                    <p class="info-text">
                        Este consiste en asignar una cuota de tráfico a cada institución.
                    </p>
                    
                    <p class="info-text">
                        Esta cuota solo cuenta el tráfico de navegación a internet, excluyendo los sitios esenciales publicados en el portal 
                        <a href="https://proxyenlaces.sld.cu" class="footer-link">proxyenlaces.sld.cu</a>, 
                        los sitios de medicina permitidos por Infomed y la red de Cuba.
                    </p>
                    
                    <p class="info-text">
                        En todo momento la institución puede conocer el estado de su cuota visitando la dirección 
                        <a href="https://cuota.hlg.sld.cu" class="footer-link">cuota.hlg.sld.cu</a> 
                        y en caso de haber consumido el 100%, recibirá esta página como respuesta a cada petición.
                    </p>
                    
                    <p class="info-text">
                        Este sistema esta concebido para estimular el uso de los recursos de información de salud que ofrece Infomed. 
                        Se recomienda a la dirección y los administradores de red de cada institución, tomar todas las medidas necesarias 
                        para garantizar que la cuota de tráfico asignada sea utilizada correctamente de acuerdo a los propósitos de la red Infomed.
                    </p>
                </div>


                </div>
            </div>
        </footer>

<!-- Copyright separado completamente del footer principal -->
<div class="copyright-section">
    <p class="copyright">
        Copyright © Nodo Infomed - Holguín 2008-<?php echo date("Y");?>
    </p>
</div>

<script>
    function actualizarDatos() {
        fetch('get_data.php')
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    document.getElementById('utilizacion').textContent = data.utilizacion + '%';
                    document.getElementById('cuota-asignada').textContent = data.CuotaAsignada;
                    document.getElementById('disponibilidad').textContent = data.disponibilidad;
                    document.getElementById('consumo').textContent = data.ConsumoUser;
                    document.getElementById('consumo-24h').textContent = data.used_24h;
                    
                    const progressBar = document.querySelector('.progress-bar');
                    progressBar.style.width = data.utilizacion + '%';
                    progressBar.setAttribute('data-progress', data.utilizacion);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Actualizar cada 30 segundos
    setInterval(actualizarDatos, 30000);
</script>

    </div>
</body>
</html>