# NovaHarvest -- Proyecto UT1

**IES Valle del Jerte · Curso 2026-2027**

NovaHarvest es una aplicación web para una cooperativa agrícola del
Valle del Jerte. Su objetivo es conectar a los productores locales con
compradores interesados en aprovechar los excedentes agrícolas.

El proyecto está desarrollado con Docker Compose y utiliza Nginx,
PHP-FPM, MariaDB y Adminer.

------------------------------------------------------------------------

## 1. Descripción del proyecto

En el Valle del Jerte se producen frutas y verduras que, en ocasiones,
no llegan a venderse. NovaHarvest pretende facilitar que esos excedentes
puedan llegar a residencias, comedores sociales, restaurantes y
particulares.

La aplicación permite:

-   Consultar los excedentes agrícolas disponibles.
-   Conocer el productor, el pueblo, los kilos y el precio de cada
    producto.
-   Solicitar avisos cuando haya excedentes de un producto.
-   Consultar un calendario de productos de temporada.
-   Comprobar el estado del servidor y de la base de datos.

El proyecto se ha realizado por fases, comprobando el funcionamiento de
cada servicio antes de incorporar el siguiente.

## 2. Arquitectura

### 2.1. Servicios utilizados

  Servicio   Imagen                 Función
  ---------- ---------------------- --------------------------------------
  Nginx      `nginx:1.27-alpine`    Servidor web y acceso HTTPS
  PHP-FPM    `php:8.3-fpm-alpine`   Ejecuta los archivos PHP
  MariaDB    `mariadb:11`           Almacena los datos
  Adminer    `adminer:5`            Permite administrar la base de datos

PHP utiliza una imagen propia construida mediante `php/Dockerfile`, que
instala las extensiones `pdo_mysql` y `mysqli`.

### 2.2. Diagrama de arquitectura

``` text
                   NAVEGADOR
                       |
               HTTP / HTTPS
                       |
             Puertos 8080 / 8443
                       |
                 [ NGINX ]
                       |
                 FastCGI :9000
                       |
                  [ PHP-FPM ]
                       |
                MariaDB :3306
                       |
                  [ MariaDB ]
                       |
                  Volumen
                    dbdata


          [ ADMINER ]
               |
       Puerto 127.0.0.1:8081
               |
            MariaDB
```

### 2.3. Puertos

  Servicio        Puerto interno   Puerto del equipo
  ------------- ---------------- -------------------
  Nginx HTTP                  80                8080
  Nginx HTTPS                443                8443
  PHP-FPM                   9000        No publicado
  MariaDB                   3306        No publicado
  Adminer                   8080      127.0.0.1:8081

Los servicios PHP-FPM y MariaDB no publican sus puertos al exterior.
Adminer solamente está disponible desde el propio equipo.

### 2.4. Volúmenes

  -----------------------------------------------------------------------
  Volumen o montaje                   Función
  ----------------------------------- -----------------------------------
  `./web:/usr/share/nginx/html:ro`    Comparte los archivos de la web con
                                      Nginx

  `./web:/usr/share/nginx/html:ro` en Permite que PHP encuentre los
  PHP                                 mismos archivos que Nginx

  `./nginx/default.conf`              Configuración de Nginx

  `./nginx/certs`                     Certificados HTTPS

  `./php/www.conf`                    Configuración de PHP-FPM

  `./php/php.ini`                     Configuración de PHP

  `dbdata`                            Guarda los datos de MariaDB de
                                      forma persistente

  `./db/novaharvest.sql`              Script inicial de creación de la
                                      base de datos
  -----------------------------------------------------------------------

### 2.5. Recorrido de una petición PHP

Cuando un usuario solicita `/excedentes.php`, ocurre lo siguiente:

1.  El navegador realiza una petición a Nginx.
2.  Nginx recibe la petición mediante HTTP o HTTPS.
3.  Nginx identifica que se trata de un archivo PHP.
4.  La petición se envía a PHP-FPM mediante FastCGI por el puerto 9000.
5.  PHP-FPM ejecuta el archivo y se conecta a MariaDB mediante el
    servicio `db`.
6.  MariaDB devuelve los datos solicitados.
7.  PHP genera el HTML con los resultados.
8.  Nginx entrega la respuesta al navegador.

## 3. Arranque del proyecto

### 3.1. Requisitos previos

Para ejecutar NovaHarvest se necesita:

-   Docker.
-   Docker Compose.
-   OpenSSL para generar los certificados.
-   Un navegador web.

No es necesario instalar PHP ni MariaDB directamente en el equipo.

### 3.2. Descargar el repositorio

Clonar el repositorio:

``` bash
git clone https://github.com/vjp-jaimeMP/PIATEX.git
```

Entrar en la carpeta:

``` bash
cd NovaHarvest
```

### 3.3. Crear el archivo de variables

Copiar el archivo de ejemplo:

``` bash
cp .env.example .env
```

Editar el archivo `.env` y establecer las contraseñas propias:

``` env
DB_NAME=novaharvest
DB_USER=novaharvest_app
DB_PASS=CAMBIAR_POR_UNA_CONTRASENA
DB_ROOT_PASSWORD=CAMBIAR_POR_OTRA_CONTRASENA
```

Estas variables se utilizan para configurar MariaDB y permitir que PHP
se conecte a la base de datos.

El archivo `.env` no se sube al repositorio porque contiene información
privada.

### 3.4. Generar los certificados HTTPS

Crear la carpeta de certificados:

``` bash
mkdir -p nginx/certs
```

Generar el certificado autofirmado:

``` bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
-keyout nginx/certs/novaharvest.key \
-out nginx/certs/novaharvest.crt \
-subj "/C=ES/ST=Caceres/L=Piornal/O=NovaHarvest/CN=localhost"
```

Se generan dos archivos:

-   `novaharvest.crt`: certificado digital.
-   `novaharvest.key`: clave privada.

Estos archivos no se suben al repositorio.

### 3.5. Construir y arrancar los contenedores

Desde la carpeta principal del proyecto ejecutar:

``` bash
docker compose up -d --build
```

Este comando construye la imagen de PHP y arranca todos los servicios.

El script `db/novaharvest.sql` se ejecuta automáticamente cuando MariaDB
inicializa por primera vez una base de datos con el volumen vacío.

Para comprobar que la configuración es correcta:

``` bash
docker compose config
```

Comprobar los contenedores:

``` bash
docker compose ps
```

Consultar los registros:

``` bash
docker compose logs
```

### 3.6. Direcciones de acceso

  Página                    Dirección
  ------------------------- ---------------------------------------
  Inicio                    https://localhost:8443/
  Excedentes                https://localhost:8443/excedentes.php
  Formulario de avisos      https://localhost:8443/avisos.html
  Calendario de temporada   https://localhost:8443/temporada.md
  Estado del servidor       https://localhost:8443/estado.php
  Adminer                   http://localhost:8081

Al acceder por primera vez a HTTPS, el navegador puede mostrar un aviso
porque se utiliza un certificado autofirmado. Es necesario aceptar la
excepción para continuar en el entorno de pruebas.

Las peticiones HTTP realizadas al puerto 8080 se redirigen
automáticamente a HTTPS por el puerto 8443.

### 3.7. Acceso a Adminer

Para entrar en Adminer se utilizan los siguientes datos:

  Campo           Valor
  --------------- --------------------------
  Sistema         MySQL
  Servidor        `db`
  Usuario         `novaharvest_app`
  Contraseña      La definida en `DB_PASS`
  Base de datos   `novaharvest`

Desde Adminer se pueden consultar las tablas y comprobar los datos
almacenados.

### 3.8. Parar y volver a iniciar el proyecto

Para detener los contenedores sin eliminar los datos:

``` bash
docker compose down
```

Para volver a iniciar el proyecto:

``` bash
docker compose up -d
```

Los datos de MariaDB se mantienen porque están guardados en el volumen
`dbdata`.

**Para empezar desde cero:**

``` bash
docker compose down -v
docker compose up -d --build
```

Este procedimiento elimina también el volumen de la base de datos y sus
datos. Debe utilizarse solamente cuando se quiera reiniciar
completamente el proyecto.

## 4. Configuración

### 4.1. Archivo compose.yaml

El archivo `compose.yaml` define los cuatro servicios del proyecto.

Sus funciones principales son:

-   Crear el servidor Nginx.
-   Construir la imagen de PHP-FPM.
-   Configurar MariaDB y sus variables de entorno.
-   Crear el servicio Adminer.
-   Publicar únicamente los puertos necesarios.
-   Montar los archivos y volúmenes.
-   Configurar las dependencias entre servicios.
-   Comprobar el estado de MariaDB mediante `healthcheck`.

PHP y Adminer esperan a que MariaDB esté disponible mediante:

``` yaml
condition: service_healthy
```

### 4.2. Configuración de Nginx

El archivo `nginx/default.conf` contiene la configuración del servidor
web.

Sus principales funciones son:

-   Escuchar en el puerto 80 y redirigir a HTTPS.
-   Escuchar mediante TLS en el puerto 443.
-   Utilizar el certificado y la clave privada.
-   Servir los archivos estáticos.
-   Enviar los archivos PHP a PHP-FPM.
-   Mostrar las páginas personalizadas de error.
-   Activar la compresión GZIP.
-   Servir los archivos Markdown como texto.
-   Limitar el tamaño de las peticiones a 64 KB.

### 4.3. Configuración de PHP-FPM

El directorio `php/` contiene tres archivos:

  -----------------------------------------------------------------------
  Archivo                             Función
  ----------------------------------- -----------------------------------
  `Dockerfile`                        Construye la imagen de PHP e
                                      instala las extensiones necesarias

  `www.conf`                          Configura el grupo de procesos
                                      PHP-FPM

  `php.ini`                           Establece las opciones generales de
                                      PHP
  -----------------------------------------------------------------------

El pool de PHP-FPM utiliza el usuario y grupo `www-data` y escucha en el
puerto 9000.

En `php.ini` se configuran, entre otros valores:

``` ini
memory_limit = 128M
error_reporting = E_ALL
display_errors = Off
log_errors = On
date.timezone = Europe/Madrid
```

La opción `display_errors = Off` evita mostrar errores internos al
visitante. Los errores se mantienen en los registros del servidor.

### 4.4. Variables de entorno

El archivo `.env` contiene las variables necesarias para la conexión con
MariaDB.

  Variable             Función
  -------------------- -----------------------------------------
  `DB_NAME`            Nombre de la base de datos
  `DB_USER`            Usuario de la aplicación
  `DB_PASS`            Contraseña del usuario de la aplicación
  `DB_ROOT_PASSWORD`   Contraseña del administrador de MariaDB

PHP recibe solamente `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`. No
recibe la contraseña de root.

El archivo `.env.example` permite conocer las variables necesarias sin
publicar las contraseñas reales.

## 5. Base de datos

### 5.1. Nombre de la base de datos

La base de datos utilizada por NovaHarvest se llama:

``` text
novaharvest
```

Se utiliza MariaDB 11 y los datos se almacenan en el volumen persistente
`dbdata`.

### 5.2. Tabla excedentes

La tabla `excedentes` almacena los productos agrícolas disponibles.

  Campo         Tipo                             Descripción
  ------------- -------------------------------- ----------------------
  `id`          INT AUTO_INCREMENT PRIMARY KEY   Identificador
  `producto`    VARCHAR(100) NOT NULL            Nombre del producto
  `productor`   VARCHAR(100) NOT NULL            Nombre del productor
  `pueblo`      VARCHAR(100) NOT NULL            Localidad
  `kilos`       INT NOT NULL DEFAULT 0           Cantidad disponible
  `precio_kg`   DECIMAL(6,2) NOT NULL            Precio por kilogramo

La página `excedentes.php` consulta esta tabla y muestra solamente los
productos que tienen más de cero kilos disponibles.

### 5.3. Tabla avisos

La tabla `avisos` almacena las solicitudes realizadas mediante el
formulario.

  Campo        Tipo                                 Descripción
  ------------ ------------------------------------ ----------------------
  `id`         INT AUTO_INCREMENT PRIMARY KEY       Identificador
  `nombre`     VARCHAR(100) NOT NULL                Nombre del comprador
  `email`      VARCHAR(150) NOT NULL                Correo electrónico
  `producto`   VARCHAR(100) NOT NULL                Producto de interés
  `fecha`      DATETIME DEFAULT CURRENT_TIMESTAMP   Fecha del aviso

Cuando un usuario envía el formulario, `avisos.php` guarda los datos
mediante PDO y consultas preparadas.

### 5.4. Usuario de la aplicación

La aplicación utiliza el usuario:

``` text
novaharvest_app
```

Este usuario tiene únicamente los permisos:

-   `SELECT`: consultar los datos.
-   `INSERT`: insertar nuevos avisos.

Los permisos se aplican sobre `novaharvest.*`.

No dispone de permisos para borrar tablas, modificar su estructura o
administrar la base de datos. De esta manera, si la aplicación tiene
algún problema, sus permisos están limitados.

### 5.5. Comprobar los permisos

Para consultar los privilegios del usuario:

``` bash
docker compose exec db mariadb -u root -p
```

Dentro de MariaDB:

``` sql
SHOW GRANTS FOR 'novaharvest_app'@'%';
```

Para consultar las tablas:

``` sql
USE novaharvest;
SHOW TABLES;
```

Para consultar los excedentes:

``` sql
SELECT * FROM excedentes;
```

Para consultar los avisos:

``` sql
SELECT * FROM avisos;
```

## 6. Páginas de la aplicación

### 6.1. Inicio

`index.html` presenta NovaHarvest y explica el objetivo de la
cooperativa.

Incluye el logotipo, el menú de navegación y una descripción de los
productos locales y de temporada.

### 6.2. Excedentes

`excedentes.php` consulta MariaDB y muestra los productos disponibles.

La información se obtiene mediante PDO y se presenta en una tabla con:

-   Producto.
-   Productor.
-   Pueblo.
-   Kilos disponibles.
-   Precio por kilogramo.

Los precios se muestran con dos decimales, coma decimal y el símbolo del
euro.

### 6.3. Formulario de avisos

`avisos.html` contiene un formulario con tres campos:

-   Nombre.
-   Correo electrónico.
-   Producto que interesa al comprador.

El formulario envía los datos mediante POST a `avisos.php`.

`avisos.php` utiliza una consulta preparada para guardar la solicitud en
MariaDB y muestra una confirmación al usuario.

### 6.4. Estado del servidor

`estado.php` muestra:

-   La fecha y hora del servidor.
-   El nombre del contenedor PHP.
-   La versión de MariaDB.
-   El texto que indica que la página ha sido generada por PHP-FPM.

La fecha utiliza la zona horaria `Europe/Madrid`.

### 6.5. Calendario de temporada

`temporada.md` contiene información sobre los productos del Valle del
Jerte y sus meses de temporada.

Nginx sirve este archivo como texto plano para que el navegador muestre
correctamente las tildes y las eñes.

### 6.6. Páginas de error

El proyecto incluye dos páginas personalizadas:

-   `404.html`: se muestra cuando no existe la dirección solicitada.
-   `50x.html`: se muestra cuando se produce un error interno o un
    problema de disponibilidad del servicio.

## 7. Seguridad

NovaHarvest incorpora varias medidas para limitar los riesgos:

  -----------------------------------------------------------------------
  Medida                              Función
  ----------------------------------- -----------------------------------
  Puertos limitados                   Evita publicar PHP-FPM y MariaDB
                                      directamente

  Adminer en `127.0.0.1`              Limita el acceso a la herramienta
                                      al propio equipo

  Archivo `.env`                      Mantiene las contraseñas fuera del
                                      código

  `.gitignore`                        Evita subir secretos y certificados

  Usuario `novaharvest_app`           Limita los permisos de la
                                      aplicación

  HTTPS                               Cifra la comunicación entre
                                      navegador y servidor

  Certificado autofirmado             Permite probar HTTPS en el entorno
                                      local

  `display_errors = Off`              Evita mostrar errores internos al
                                      visitante

  `client_max_body_size 64K`          Limita el tamaño de las peticiones

  Montajes de la web en solo lectura  Evitan que los contenedores
                                      modifiquen los archivos de la web
  -----------------------------------------------------------------------

El certificado es autofirmado, por lo que el navegador avisa de que no
pertenece a una autoridad certificadora reconocida. Para una instalación
pública se necesitaría un certificado emitido por una autoridad de
confianza.

## 8. Comprobaciones realizadas

Durante el desarrollo se han realizado las siguientes pruebas:

1.  Comprobación de la configuración con `docker compose config`.
2.  Construcción de los servicios con `docker compose up -d --build`.
3.  Comprobación del estado de los contenedores con `docker compose ps`.
4.  Comprobación de la configuración de Nginx con `nginx -t`.
5.  Comprobación de `display_errors` en PHP.
6.  Acceso a la portada mediante HTTPS.
7.  Comprobación de la redirección automática de HTTP a HTTPS.
8.  Consulta de los excedentes desde MariaDB.
9.  Envío de un aviso desde el formulario y comprobación de su
    almacenamiento.
10. Acceso a Adminer y consulta de las tablas.
11. Comprobación de la fecha, el contenedor PHP y la versión de MariaDB.
12. Comprobación de que los datos permanecen después de detener y volver
    a iniciar los contenedores.

## 9. Estructura del proyecto

La estructura final del repositorio es:

``` text
NovaHarvest/
├── .gitignore
├── .env.example
├── README.md
├── compose.yaml
├── cuestiones-teoricas/
│   └── cuestiones-ut1.md
├── db/
│   └── novaharvest.sql
├── nginx/
│   └── default.conf
├── php/
│   ├── Dockerfile
│   ├── www.conf
│   └── php.ini
└── web/
    ├── index.html
    ├── excedentes.php
    ├── avisos.html
    ├── avisos.php
    ├── estado.php
    ├── temporada.md
    ├── 404.html
    ├── 50x.html
    ├── css/
    │   └── estilos.css
    └── img/
        └── logo.png
```

Además, en el equipo local existen archivos que no se suben al
repositorio:

``` text
.env
nginx/certs/
├── novaharvest.crt
└── novaharvest.key
```

## 10. Control de versiones

El proyecto utiliza Git para guardar los cambios realizados durante el
desarrollo.

Las fases principales se guardan mediante commits diferenciados:

  Fase     Commit
  -------- -------------------------------------
  Fase 1   `UT1 Fase 1: servidor web estático`
  Fase 2   `UT1 Fase 2: integración PHP-FPM`
  Fase 3   `UT1 Fase 3: integración MariaDB`
  Fase 4   `UT1 Fase 4: HTTPS y seguridad`

Para consultar el historial:

``` bash
git log --oneline
```

Para comprobar los archivos ignorados:

``` bash
git check-ignore -v .env
```

El archivo `.env` y los certificados nunca deben subirse al repositorio.

------------------------------------------------------------------------

**NovaHarvest · IES Valle del Jerte · Curso 2026-2027**
