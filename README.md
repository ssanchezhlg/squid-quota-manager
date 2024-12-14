# Squid Quota Manager

Sistema integral para la gestión y control de cuotas de navegación web, diseñado específicamente para Squid Proxy. Permite administrar el consumo de datos por usuario/IP con reseteo automático configurable.

## Características Principales
- 🚀 Monitoreo en tiempo real del consumo de datos
- 💻 Interfaz administrativa en Django
- 👥 Portal de usuario en PHP para consulta de cuotas
- 📊 Reportes detallados de consumo
- ⏰ Reseteo automático de cuotas (diario/semanal/mensual/anual)
- 🔄 Integración directa con Squid Proxy
- 📱 Interfaces web responsive

## Componentes
- Backend en Python para procesamiento de logs
- Panel administrativo en Django
- Portal de usuario en PHP
- Base de datos MariaDB
- Scripts de mantenimiento automático

## Ideal Para
- Instituciones educativas
- Empresas
- Proveedores de servicios de Internet
- Administradores de redes

## Estado del Proyecto
🟢 En desarrollo activo | ✅ Estable para producción


Sistema modular para la gestión de cuotas de navegación web, desarrollado en Python, Django y PHP. Integra procesamiento de logs de Squid en tiempo real, almacenamiento en MariaDB, y múltiples interfaces web para administración y consulta de usuarios.

Stack Tecnológico:
- Backend: Python 3.x
- Framework Admin: Django
- Portal Usuario: PHP 8.0
- Base de Datos: MariaDB
- Proxy: Squid
- Servidor Web: Nginx
- Process Manager: Supervisor


# Guía de Instalación del Sistema de Cuotas

## Descripción General
Este sistema consta de dos componentes principales:
1. Servidor Squid + MariaDB + Scripts Python
2. Servidor Web (Interfaz administrativa Django + Interfaz usuario PHP)

## 1. Instalación en el Servidor Squid

### 1.1 Instalación de MariaDB
```bash
# Instalar MariaDB
apt-get install mariadb-server

# Modificar la configuración para escuchar en todas las IPs
sed -i 's/bind-address.*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mariadb.conf.d/50-server.cnf

# Reiniciar MariaDB
systemctl restart mariadb
```

### 1.2 Configuración de la Base de Datos
```sql
mysql -u root -p

-- Crear base de datos y usuarios
CREATE DATABASE pquot;
CREATE USER 'pquot'@'localhost' IDENTIFIED BY 'pquotwebdb';
GRANT ALL PRIVILEGES ON pquot.* TO 'pquot'@'localhost';

-- Crear usuario para el servidor web (ajusta la IP según tu configuración)
CREATE USER 'pquot'@'localhost' IDENTIFIED BY 'pquotwebdb';
GRANT ALL PRIVILEGES ON pquot.* TO 'pquot'@'localhost';

FLUSH PRIVILEGES;
use pquot;
source /srv/cuotas/pquot.sql;
exit;
```

### 1.3 Instalación de Scripts Python
```bash
# Crear directorios necesarios
mkdir -p /etc/pquot
mkdir -p /var/spool/pquot
mkdir -p /var/log/pquot

# Copiar scripts Python
cp check_quota.py pquot-reader.py pquot-reset.py pquot-reset-semanal.py \
   pquot-reset-anual.py pquot-reset-mensual.py pquot-updater.py \
   pquot-update-users.py /usr/local/bin/

# Dar permisos de ejecución
chmod +x /usr/local/bin/pquot-*
chmod +x /usr/local/bin/check_quota.py
```

### 1.4 Configuración de Squid
```bash
# Agregar al final de squid.conf
cat >> /etc/squid/squid.conf << EOF
# ACLs para sistema de cuotas
external_acl_type checkquota children-max=50 children-startup=25 ttl=5 %SRC /usr/local/bin/check_quota.py

acl check_quota external checkquota
deny_info https://cuota.hlg.sld.cu/ check_quota hora_full

acl hora_full time MTWHFAS 00:00-07:00

# Reglas de acceso
http_access allow internet check_quota
http_access deny internet !check_quota !hora_full

# Configuración de logs
logformat squid_account %ts.%03tu %6tr %>a %Ss/%03>Hs %<st %rm %ru %>a %Sh/%<A %mt %ea
access_log /var/log/squid/access_cuotas.log squid_account !dst_cuba !esenciales1 !esenciales2 !esenciales3 !localhost
EOF

# Reiniciar Squid
systemctl restart squid
```

## 2. Instalación del Servidor Web

### 2.1 Instalación de Dependencias
```bash
# Instalar paquetes necesarios
apt install python3 python3-pip python3-dev python3-venv nginx supervisor php8.0 php8.0-fpm php8.0-mysql
```

### 2.2 Configuración de la Interfaz Administrativa (Django)
```bash
# Crear estructura de directorios
mkdir -p /srv/cuotas/pquotadmin
cd /srv/cuotas/pquotadmin

# Crear y activar entorno virtual
python3 -m venv env
source env/bin/activate

# Instalar dependencias
pip install -r requirements.txt
```

### 2.3 Configuración de Django
```python
# Editar /srv/cuotas/pquotadmin/settings.py
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'pquot',
        'USER': 'pquot',
        'PASSWORD': 'pquotwebdb',
        'HOST': 'localhost',
        'PORT': '3306',
    }
}
```

### 2.4 Configuración de Gunicorn
```bash
# Crear script de inicio
cat > /srv/cuotas/pquotadmin/gunicorn_start.sh << EOF
#!/bin/bash
NAME="admin_pquot"
DJANGODIR=/srv/cuotas/pquotadmin
SOCKFILE=/srv/cuotas/pquotadmin/run/gunicorn.sock
USER=www-data
GROUP=www-data
NUM_WORKERS=3
DJANGO_SETTINGS_MODULE=pquotadmin.settings
DJANGO_WSGI_MODULE=pquotadmin.wsgi

cd \$DJANGODIR
source env/bin/activate
export DJANGO_SETTINGS_MODULE=\$DJANGO_SETTINGS_MODULE
export PYTHONPATH=\$DJANGODIR:\$PYTHONPATH

RUNDIR=\$(dirname \$SOCKFILE)
test -d \$RUNDIR || mkdir -p \$RUNDIR

exec env/bin/gunicorn \${DJANGO_WSGI_MODULE}:application \
  --name \$NAME \
  --workers \$NUM_WORKERS \
  --user=\$USER \
  --group=\$GROUP \
  --bind=unix:\$SOCKFILE \
  --log-level=debug \
  --log-file=-
EOF

chmod +x /srv/cuotas/pquotadmin/gunicorn_start.sh
```

## 2.5 Configuración de Supervisor
````bash
# Crear archivo de configuración para Supervisor
cat > /etc/supervisor/conf.d/pquot_admin.conf << EOF
[program:pquot_admin]
command=/srv/cuotas/pquotadmin/gunicorn_start.sh
user=www-data
directory=/srv/cuotas/pquotadmin
autostart=true
autorestart=true
stderr_logfile=/var/log/pquotadmin/gunicorn.err.log
stdout_logfile=/var/log/pquotadmin/gunicorn.out.log
EOF

# Crear directorio para logs
mkdir -p /var/log/pquotadmin

# Reiniciar Supervisor
supervisorctl reread
supervisorctl update
supervisorctl start pquot_admin
````

### 2.6 Configuración de la Interfaz de Usuario (PHP)
````bash
# Crear directorio para la interfaz de usuario
mkdir -p /srv/cuotas/pquotuser
# Copiar archivos PHP de la interfaz de usuario
cp -r /ruta/origen/archivos_php/* /srv/cuotas/pquotuser/

# Configurar permisos
chown -R www-data:www-data /srv/cuotas/pquotuser
chmod -R 755 /srv/cuotas/pquotuser
````

### 2.7 Configuración de Nginx

#### Para la interfaz administrativa:
````bash
cat > /etc/nginx/sites-available/admincuotas << EOF
server {
    listen 80;
    server_name admincuotas.hlg.sld.cu;

    access_log /var/log/nginx/AdminCuotas.access.log;
    error_log /var/log/nginx/AdminCuotas.error.log;

    location / {
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;

        proxy_pass http://unix:/srv/cuotas/pquotadmin/run/gunicorn.sock;
        proxy_read_timeout 90;
    }

    location /static/ {
        alias /srv/cuotas/pquotadmin/pquotapp/static/;
        expires 30d;
    }
}
EOF
````

#### Para la interfaz de usuario:
````bash
cat > /etc/nginx/sites-available/cuotas << EOF
server {
    listen 80;
    server_name cuotas.hlg.sld.cu;
    
    access_log /var/log/nginx/SistemaCuotas-access.log;
    error_log /var/log/nginx/SistemaCuotas-error.log;
    
    root /srv/cuotas/pquotuser;
    index index.html index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_send_timeout 5m;
        fastcgi_read_timeout 5m;
        fastcgi_connect_timeout 5m;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOF

# Habilitar los sitios
ln -s /etc/nginx/sites-available/admincuotas /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/cuotas /etc/nginx/sites-enabled/

# Verificar configuración y reiniciar Nginx
nginx -t
systemctl restart nginx
````

## 3. Tareas de Mantenimiento

### 3.1 Configuración de Tareas Programadas
````bash
# Añadir al crontab
crontab

# Agregar las siguientes líneas:

## Sistema de cuotas de Infomed cada 10 segundos
*/1 * * * * root python /usr/local/bin/pquot-updater.py
*/1 * * * * root sleep 10 && python /usr/local/bin/pquot-updater.py
*/1 * * * * root sleep 20 && python /usr/local/bin/pquot-updater.py
*/1 * * * * root sleep 30 && python /usr/local/bin/pquot-updater.py
*/1 * * * * root sleep 40 && python /usr/local/bin/pquot-updater.py
*/1 * * * * root sleep 50 && python /usr/local/bin/pquot-updater.py

*/1 * * * * root python /usr/local/bin/pquot-update-users.py
*/1 * * * * root sleep 10 && python /usr/local/bin/pquot-update-users.py
*/1 * * * * root sleep 20 && python /usr/local/bin/pquot-update-users.py
*/1 * * * * root sleep 30 && python /usr/local/bin/pquot-update-users.py
*/1 * * * * root sleep 40 && python /usr/local/bin/pquot-update-users.py
*/1 * * * * root sleep 50 && python /usr/local/bin/pquot-update-users.py

###Resetear cuota cada 1 hora###
0 */1 * * * root python /usr/local/bin/pquot-reset.py && /etc/init.d/squid reload > /dev/null 2>&1


# Para reset mensual (ejecutar el primer día de cada mes a las 00:01)
1 0 1 * *   root python /usr/local/bin/pquot-reset-mensual.py

# Para reset anual (ejecutar el primer día del año a las 00:01)
1 0 1 1 *   root python /usr/local/bin/pquot-reset-anual.py

# Para reset semanal (ejecutar cada lunes a las 00:01)
1 0 * * 1   root python /usr/local/bin/pquot-reset-semanal.py
````

### 3.2 Verificación del Sistema
````bash
# Verificar estado de los servicios
systemctl status mariadb
systemctl status nginx
systemctl status php8.0-fpm
supervisorctl status pquot_admin

# Verificar logs
tail -f /var/log/nginx/AdminCuotas.error.log
tail -f /var/log/pquotadmin/gunicorn.err.log
tail -f /var/log/squid/access_cuotas.log
````

## 4. Notas Importantes
1. Ajustar las IPs y nombres de dominio según tu entorno
2. Asegurar que los puertos necesarios estén abiertos en el firewall
3. Configurar copias de seguridad de la base de datos
4. Revisar periódicamente los logs del sistema
5. Mantener actualizados los componentes del sistema

