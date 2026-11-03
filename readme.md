# Proyecto NovaHarvest - Fase UT1

Este proyecto está inspirado en el proyecto PIATEX que participa el IES Valle del Jerte (Plasencia) en el curso 2026-2027.

---

## Contexto

En el Valle del Jerte, una cooperativa agraria y sus productores tienen cada semana fruta, verdura y otros productos que no llegan a venderse: cereza que madura a la vez, tomate que no aguanta el transporte, calabacín que sobra de una cosecha. Parte acaba en la basura.

Al otro lado hay **compradores** que podrían aprovecharlo: residencias, comedores sociales, restaurantes y particulares. Lo que falta es una herramienta que los conecte.

La cooperativa quiere una aplicación web, **NovaHarvest**, que publique los excedentes de los productores y avise a los compradores interesados. La aplicación la programa el equipo de desarrollo, pero antes necesita **dónde vivir**: un servidor instalado, configurado, seguro y documentado. Ese es tu trabajo como **administrador de sistemas**.

Mientras tanto, la cooperativa quiere publicar ya una primera web de NovaHarvest: una portada, el listado de excedentes disponibles (leído de una base de datos) y un formulario para que los compradores pidan que se les avise. En esta fase debes montar **la infraestructura** que la sostiene:

- Un **servidor web** (nginx): puerta de entrada, ficheros estáticos y HTTPS.
- Un **procesador de código** (PHP-FPM) que ejecuta el PHP en el servidor.
- Un **sistema gestor de bases de datos** (MariaDB) que guarda los datos.
- Una **utilidad de pruebas** (Adminer) para administrar la base de datos.
- Todo con **Docker Compose**: reproducible, con un único comando y sin exponer más de lo necesario.

Es el mismo camino que hemos seguido con **CaféAroma**. Puedes revisar conceptos, configuración y código ya implementados.

---

## Normas generales

### Solo lo visto en clase

> **Solo puedes usar lo que hemos visto en clase.** Una parte del proyecto hecha con algo no visto en clase **no se puntúa**, aunque funcione. Si dudas de si algo se puede usar, pregúntalo **antes** de usarlo.

> **AUTORÍA**: Si hay duda acerca de si algún código o configuración ha sido generado o copiado, se preguntará al alumno directamente acerca de dicha implementación. Si no sabe responder SE CONSIDERA como generado o copiado, por lo que automaticamente tendrá una calificación de 0.

### Configuración

1. Las directivas de configuración (nginx, PHP-FPM, `php.ini`, Compose) son las vistas en clase. Las únicas nuevas son las que **nombra este enunciado**.
2. Los nombres de servicios, ficheros, tablas, columnas, variables y usuarios son **exactamente** los del enunciado.
3. Cada fichero de configuración (`compose.yaml`, `nginx/default.conf`, `php/Dockerfile`, `php/www.conf`, `php/php.ini` y `db/novaharvest.sql`) lleva **comentarios breves** que explican para qué sirve cada bloque.
4. **Ninguna contraseña** aparece en un fichero del repositorio, ni en el historial de git.
5. Los textos que ve el usuario en las páginas van **en español**.

### PHP

> El PHP de esta UT es el **mínimo** para comprobar que el stack funciona: la programación en PHP llega en UT5 y UT6.

Solo puedes usar lo visto en las sesiones 9 y 11: variables, arrays, `foreach`, `echo` y `<?= ?>`, `date()`, `getenv()`, `sprintf()`, `number_format()`, `$_POST` y PDO (`new PDO`, `setAttribute`, `query`, `fetchAll`, `fetchColumn`, `prepare` y `execute`).

### Control de versiones

1. El proyecto es un **repositorio git nuevo** (`novaharvest-iaplw`) desde el primer día.
2. Debe haber **commits**, con mensajes que describan el cambio. El historial debe mostrar cómo ha ido creciendo el proyecto: **no se acepta** un único commit con todo hecho.
3. Cada **fase** del apartado 2 termina en un commit cuyo mensaje empieza por `UT1 Fase 1:`, `UT1 Fase 2:`, `UT1 Fase 3:` o `UT1 Fase 4:`. Al volver a ese commit, la fase debe **funcionar**.
4. El `.gitignore` existe desde el **primer commit** y excluye `.env` y `nginx/certs/`.

---

## 1. Estructura final

> **Requisito:** cualquiera (el profesor, un compañero, la cooperativa) debe poder clonar el repositorio y saber dónde está la configuración de cada servicio.

Al terminar, el repositorio DEBE tener **exactamente** la siguiente estructura:

> Para cumplir con el punto 3 del apartado anterior, no copiéis la estructura todavía, id poco a poco.

```text
novaharvest-iaplw/
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

Además, en tu equipo (pero **nunca** en el repositorio) existen:

```text
novaharvest-iaplw/
├── .env                      # contraseñas reales
└── nginx/certs/
    ├── novaharvest.crt       # certificado autofirmado
    └── novaharvest.key       # clave privada
```

| Pieza | Qué es |
|---|---|
| `compose.yaml` | Describe el entorno completo: servicios, puertos, volúmenes y cómo se conectan |
| `.env.example` | Qué variables necesita el proyecto, sin valores secretos |
| `nginx/` | La configuración del servidor web |
| `php/` | Cómo se construye la imagen de PHP-FPM y su configuración |
| `db/` | El script que crea las tablas, los datos iniciales y los privilegios |
| `web/` | Los ficheros que sirve nginx (estáticos) y los que ejecuta PHP-FPM (`.php`) |

`logo.png` es una imagen libre (el logotipo que debes diseñar para NovaHarvest).

---

## 2. Fases

> **Requisito:** la infraestructura debe montarse **paso a paso**, comprobando que cada pieza funciona antes de añadir la siguiente.

### Fase 1: nginx sirve la web estática

1. `compose.yaml` con el servicio `web` (apartado 3.1), publicando solo el `8080`.
2. Los ficheros estáticos del apartado 4: `index.html`, `avisos.html`, `404.html`, `50x.html`, `temporada.md`, `css/estilos.css` e `img/logo.png`.
3. `nginx/default.conf` con un único `server` en el puerto 80 y los puntos 3 a 6 del apartado 3.3.
4. `http://localhost:8080/` muestra la portada.

### Fase 2: PHP-FPM procesa el código

1. `php/Dockerfile`, `php/www.conf` y `php/php.ini` (apartado 3.4), y el servicio `php`.
2. El `location` de PHP en `default.conf` (punto 8 del apartado 3.3).
3. `estado.php` con las líneas de fecha y contenedor (apartado 4.5). La línea de la base de datos llega en la fase 3.

### Fase 3: MariaDB y acceso a datos

1. `.env` y `.env.example` (apartado 3.5).
2. El servicio `db` con su volumen, y las variables `DB_*` en el servicio `php`.
3. `db/novaharvest.sql` cargado: tablas, datos y privilegios del apartado 3.5.
4. `excedentes.php`, `avisos.php` y la línea de la base de datos en `estado.php`.

### Fase 4: integración y seguridad

1. `healthcheck` en `db`: `php` y `adminer` esperan a que esté `healthy`.
2. El servicio `adminer`, accesible solo desde el propio equipo.
3. HTTPS con certificado autofirmado y redirección desde HTTP (apartado 3.3).
4. `client_max_body_size` y `display_errors = Off`.
5. La estructura queda **exactamente** como en el apartado 1.

---

## 3. Entorno

> **Requisito:** NovaHarvest debe levantarse **con un único comando**, sin instalar nada más que Docker, y desde fuera solo debe ser accesible lo imprescindible.

### 3.1. Servicios

| Servicio (`compose.yaml`) | Imagen | Puertos publicados | Volúmenes |
|---|---|---|---|
| `web` | `nginx:1.27-alpine` | `8080` -> `80` y `8443` -> `443` | `./web` (solo lectura), `nginx/default.conf` y `nginx/certs/` |
| `php` | Construida desde `php/` | **Ninguno** | `./web` en la **misma ruta** que `web`, `www.conf` y `php.ini` |
| `db` | `mariadb:11` | **Ninguno** | Volumen con nombre `dbdata` en `/var/lib/mysql` |
| `adminer` | `adminer:5` | `127.0.0.1:8081` -> `8080` | - |

Ninguna imagen se usa con la etiqueta `latest` ni sin etiqueta.

### 3.2. Requisitos generales

1. Con `.env` y los certificados creados, `docker compose up -d --build` levanta todo **a la primera**.
2. `web` arranca después de `php`. `php` y `adminer` esperan a que `db` esté `healthy` (`condition: service_healthy`).
3. `db` tiene el `healthcheck` visto en clase (`healthcheck.sh --connect --innodb_initialized`).
4. `php` recibe **solo** `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`. La contraseña de `root` **no** llega a `php`.

### 3.3. nginx (`nginx/default.conf`)

1. Un `server` en el puerto `80` que **solo** redirige con `301` a la misma URL en `https://` y puerto `8443`.
2. Un `server` en el puerto `443` con TLS, que usa `/etc/nginx/certs/novaharvest.crt` y `/etc/nginx/certs/novaharvest.key`, y contiene todo lo siguiente.
3. `root /usr/share/nginx/html` e `index index.php index.html`.
4. `404.html` para el error `404`, y `50x.html` para `500`, `502`, `503` y `504`. Ninguna de las dos es accesible directamente por su URL.
5. Compresión `gzip` para `text/css`, `application/javascript` y `application/json`, a partir de 256 bytes.
6. Los ficheros `.md` se sirven como `text/plain; charset=utf-8`.
7. El cuerpo de una petición no puede superar **64 KB** (`client_max_body_size`): un formulario de avisos nunca necesita más.
8. Los `.php` se pasan a `php:9000` por FastCGI, con `SCRIPT_FILENAME` construido a partir del `document_root`.

El certificado es **autofirmado**, para `CN=localhost`, y se genera con `openssl` como en clase.

### 3.4. PHP-FPM (`php/`)

| Fichero | Dentro del contenedor | Contenido |
|---|---|---|
| `Dockerfile` | (construye la imagen) | Parte de `php:8.3-fpm-alpine` e instala las extensiones `pdo_mysql` y `mysqli` |
| `www.conf` | `/usr/local/etc/php-fpm.d/www.conf` | Pool `[www]`: usuario y grupo `www-data`, escucha en `9000`, `pm = dynamic` con los valores vistos en clase (`max_children` 5, `start_servers` 2, `min_spare_servers` 1, `max_spare_servers` 3) |
| `php.ini` | `/usr/local/etc/php/conf.d/novaharvest.ini` | `memory_limit = 128M`, `error_reporting = E_ALL`, `display_errors = Off`, `log_errors = On` y `date.timezone = Europe/Madrid` |

`date.timezone` es la única directiva nueva: fija la zona horaria con la que PHP muestra fechas y horas. Mientras desarrollas puedes tener `display_errors = On`; la entrega (fase 4) va con `Off`.

### 3.5. MariaDB (`db/` y `.env`)

#### Variables

`.env` define estas cuatro variables, que el `compose.yaml` usa con `${VAR}` en `db` y en `php`:

| Variable | Valor |
|---|---|
| `DB_NAME` | `novaharvest` |
| `DB_USER` | `novaharvest_app` |
| `DB_PASS` | La que elijas |
| `DB_ROOT_PASSWORD` | La que elijas |

`.env.example` tiene las mismas variables, con los nombres de la base de datos y del usuario, y las contraseñas (ejecutaré `cp .env.example .env` para poder levantar todo).

#### `db/novaharvest.sql`

Se ejecuta **una vez**, como `root`, tras el primer arranque de `db`. Empieza por `USE novaharvest;` y hace, en este orden:

1. Crea la tabla `excedentes`:

| Columna | Tipo |
|---|---|
| `id` | `INT AUTO_INCREMENT PRIMARY KEY` |
| `producto` | `VARCHAR(100) NOT NULL` |
| `productor` | `VARCHAR(100) NOT NULL` |
| `pueblo` | `VARCHAR(100) NOT NULL` |
| `kilos` | `INT NOT NULL DEFAULT 0` |
| `precio_kg` | `DECIMAL(6,2) NOT NULL` |

2. Crea la tabla `avisos`:

| Columna | Tipo |
|---|---|
| `id` | `INT AUTO_INCREMENT PRIMARY KEY` |
| `nombre` | `VARCHAR(100) NOT NULL` |
| `email` | `VARCHAR(150) NOT NULL` |
| `producto` | `VARCHAR(100) NOT NULL` |
| `fecha` | `DATETIME DEFAULT CURRENT_TIMESTAMP` |

3. Inserta estos excedentes, en este orden:

| `producto` | `productor` | `pueblo` | `kilos` | `precio_kg` |
|---|---|---|---|---|
| Cereza Picota | Huerta Los Castaños | Cabezuela del Valle | 223 | 2.24 |
| Castaña | Finca El Rebollar | Rebollar | 150 | 2.10 |
| Higo | Huerto La Garganta | Navaconcejo | 35 | 2.80 |
| Tomate de colgar | Hermanos Sánchez | Jerte | 80 | 1.10 |
| Calabacín | Huerta Los Castaños | Cabezuela del Valle | 0 | 0.90 |

4. Deja a `novaharvest_app` con **solo** los privilegios que necesita la web: `SELECT` e `INSERT` sobre `novaharvest.*`.

> Recuerda que las variables `MARIADB_*` solo actúan con el volumen **vacío** (sesión 10).

---

## 4. Páginas

> **Requisito:** nginx sirve directamente los ficheros estáticos y PHP-FPM ejecuta los `.php`, que leen y guardan los datos en MariaDB.

### 4.1. Rutas

| Ruta | Tipo | Título | Texto del enlace en el menú | Contenido |
|---|---|---|---|---|
| `/` | Estático (`index.html`) | Inicio | Inicio | Presentación de NovaHarvest: qué problema quiere resolver y para quién (3 o 4 frases) |
| `/excedentes.php` | Dinámico | Excedentes disponibles | Excedentes | Apartado 4.3 |
| `/avisos.html` | Estático | Avísame | Avísame | Apartado 4.4 |
| `/avisos.php` | Dinámico | Aviso registrado | (no está en el menú) | Apartado 4.4 |
| `/temporada.md` | Estático | - | Temporada | Apartado 4.6 |
| `/estado.php` | Dinámico | Estado del servidor | Estado | Apartado 4.5 |

| Estático | URL |
|---|---|
| Hoja de estilos | `/css/estilos.css` |
| Logotipo | `/img/logo.png` |

Cualquier otra ruta responde **404**.

### 4.2. Estructura de cada página

Todas las páginas HTML (incluidas `404.html` y `50x.html`) tienen la misma estructura:

1. HTML válido: `<!DOCTYPE html>`, `<html lang="es">` y `<meta charset="UTF-8">`.
2. `<title>`: `NovaHarvest - {título de la página}`. Para `404.html`, `Página no encontrada`; para `50x.html`, `Servicio no disponible`.
3. Enlace a la hoja de estilos `/css/estilos.css`.
4. `<header>` con el logotipo (`/img/logo.png`), el nombre `NovaHarvest` y un `<nav>` con los cinco enlaces del menú, en el orden de la tabla 4.1.
5. `<main>` con el contenido, que empieza por un `<h1>` con el título de la página.
6. `<footer>`: `NovaHarvest · IES Valle del Jerte · Curso 2026-2027`.

El diseño de `estilos.css` es libre. **NUNCA GENERADO CON IA**.

### 4.3. Excedentes disponibles (`/excedentes.php`)

Una `<table>` con una fila por cada excedente de la tabla `excedentes` que tenga **más de 0 kilos**, con estas columnas:

| Cabecera | Valor | Ejemplo |
|---|---|---|
| Producto | `producto` | `Cereza Picota` |
| Productor | `productor` | `Huerta Los Castaños` |
| Pueblo | `pueblo` | `Cabezuela del Valle` |
| Kilos | `kilos` | `223` |
| Precio/kg | `precio_kg` con dos decimales y coma decimal | `2,24 €` |

Con los datos del apartado 3.5 aparecen **4 filas**: el calabacín no sale.

La conexión usa PDO con los datos de `getenv()`: ninguna credencial está escrita en el código.

### 4.4. Avisos (`/avisos.html` y `/avisos.php`)

`avisos.html` tiene un `<form method="POST" action="/avisos.php">` con estos campos, todos con `required`, y un botón `Avísame`:

| Etiqueta | `name` | `type` |
|---|---|---|
| Nombre | `nombre` | `text` |
| Correo electrónico | `email` | `email` |
| Producto que te interesa | `producto` | `text` |

`avisos.php`:

1. Guarda el aviso en la tabla `avisos` con un *prepared statement* con parámetros con nombre (`:nombre`, `:email` y `:producto`).
2. Muestra `<h1>Aviso registrado</h1>`, el texto `Gracias. Te avisaremos cuando haya excedentes del producto que buscas.` y un enlace a `/excedentes.php`.
3. **No** muestra ningún dato enviado por el usuario.

### 4.5. Estado del servidor (`/estado.php`)

Cada línea en su propio elemento:

| Texto en la página | Cómo se obtiene |
|---|---|
| `Fecha y hora del servidor: 27/10/2026 09:31:05` | `date()`, con ese formato y la hora de España |
| `Contenedor PHP: {nombre}` | `getenv('HOSTNAME')`: el nombre que Docker da al contenedor |
| `Base de datos: MariaDB {versión}` | `SELECT VERSION()`, conectado como `novaharvest_app` |
| `Página generada por PHP-FPM` | Texto fijo |

### 4.6. Calendario de temporada (`/temporada.md`)

La cooperativa escribe el calendario en Markdown. `temporada.md` tiene un título y, al menos, cinco productos del valle con sus meses de temporada (por ejemplo, `Castaña: octubre y noviembre`). Al abrirlo, el navegador lo **muestra** como texto, con las tildes y eñes correctas.

---

## 5. Memoria (`README.md`)

> **Requisito:** quien reciba el proyecto debe poder arrancarlo sin preguntarte nada, y entender **por qué** está configurado así.

El `README.md` de la raíz del repositorio debe tener estos apartados:

1. **Arranque**: desde un clon recién descargado, todos los pasos en orden y con sus comandos: crear `.env`, generar los certificados, levantar el entorno, cargar `db/novaharvest.sql` (con lo visto en clase) y comprobar que funciona. Las URL de cada página y de Adminer. Cómo pararlo **sin perder datos** y cómo empezar de cero.
2. **Arquitectura**: un diagrama (imagen o texto) con el navegador, los cuatro servicios, los puertos (publicados e internos), los volúmenes y el recorrido de una petición a `excedentes.php`.
3. **Configuración**: una tabla con cada fichero de configuración del repositorio, el servicio que lo usa, dónde acaba dentro del contenedor y qué configura.
4. **Base de datos**: las tablas, el usuario de la aplicación, sus privilegios y por qué esos, y cómo comprobarlos.

## 5.1 Cuestiones UT1 (`cuestiones-ut1.md`)

> El propósito de este documento es entender qué estamos haciendo y por qué.

El archivo `cuestiones-ut1.md` estará en la carpeta `cuestiones-teoricas` del repositorio y debe responder a las siguientes preguntas:

> Responde **sobre tu proyecto**, no en general (entre 5 y 10 líneas cada una). Si una pregunta te pide cambiar algo para probarlo, déjalo después como estaba.

1. **Software necesario.** Haz una tabla con todo el software que necesita NovaHarvest (imágenes con su etiqueta y extensiones de PHP): qué hace cada pieza en tu proyecto y por qué esa variante (`alpine`, `fpm`...). ¿Por qué ninguna imagen usa `latest`? Copia la salida de `docker compose images` y de `docker compose exec php php -m`. ¿Qué pasaría con `excedentes.php` si usaras `php:8.3-fpm-alpine` directamente, sin tu `Dockerfile`?
2. **Tecnologías.** Explica con tu proyecto la diferencia entre CGI, FastCGI y un módulo del servidor web (Apache con `mod_php`): ¿qué proceso ejecuta `excedentes.php` en cada caso y qué pasa con 100 peticiones seguidas? ¿Qué gana la cooperativa con nginx y PHP-FPM en contenedores separados? ¿Y con contenedores en lugar de una máquina virtual por servicio? ¿Por qué PDO y no `mysqli`?
3. **Cliente y servidor.** Clasifica cada fichero de `web/` en estático o dinámico y di quién lo procesa. Mira el código fuente de `excedentes.php` en el navegador: ¿aparece tu PHP? ¿Por qué? Envía un aviso vacío saltándote el navegador (`curl -k -X POST -d "nombre=&email=&producto=" https://localhost:8443/avisos.php`) y mira en Adminer qué se ha guardado: ¿dónde se comprueba `required` y por qué no protege la base de datos? ¿Qué pasa si envías el formulario a `avisos.html`?
4. **nginx.** Navega por el sitio con `docker compose logs -f web` abierto: copia la línea de `excedentes.php` y la de `estilos.css` y explica cada campo. Provoca un 404 desde un enlace y localiza en el log desde qué página se enlazaba. Compara con `curl` los bytes de `estilos.css` con y sin `Accept-Encoding: gzip`. ¿Qué `Content-Type` tenía `temporada.md` antes de configurar su `location`, y por qué no puedes poner `types { }` en el `server`?
5. **MariaDB.** Registra un aviso, haz `docker compose down` y `docker compose up -d`, y comprueba que sigue ahí: ¿dónde estaba guardado? (`docker volume ls` e `inspect`). ¿Qué haría `docker compose down -v`? Cambia `DB_PASS` en `.env`, recrea los contenedores y abre `excedentes.php`: copia el error (de la página o de `docker compose logs php`) y explica por qué ocurre. Compara la `fecha` de un aviso en Adminer con la hora de `estado.php` en el mismo momento: ¿coinciden? ¿Por qué?
6. **Ejecución de un `.php`.** Explica paso a paso qué ocurre desde que el navegador pide `http://localhost:8080/excedentes.php` hasta que recibe la tabla: contenedores, puertos, protocolos (HTTP, HTTPS, FastCGI y el de MariaDB) y las directivas de tu `default.conf` que intervienen. ¿Por qué `web` y `php` montan `./web` en la misma ruta? Cámbiala en `php` (por ejemplo, a `/var/www/html`) y copia lo que ves en el navegador y en `docker compose logs php`. Abre `/nada.html` y `/nada.php`: ¿quién responde en cada caso y por qué en el segundo no sale tu `404.html`?
7. **PHP-FPM.** Copia la salida de `docker compose exec php ps aux` y explica cada proceso y su usuario, relacionándolo con tu `www.conf`. ¿Qué significan tus valores de `pm.*`? ¿Qué le pasaría a la sexta petición si llegaran seis a la vez y todas tardaran mucho? Copia la salida de `docker compose exec php php --ini` y explica cómo llega tu `php.ini` al contenedor. Quita `date.timezone`, recrea y compara la hora de `estado.php`.
8. **Acceso a la base de datos.** Sigue el recorrido de `DB_PASS` desde `.env` hasta `new PDO(...)`, con evidencias: `docker compose config`, `docker compose exec php env | grep DB_` y tu código. ¿Por qué funciona `host=db`? ¿Por qué `php` no recibe `DB_ROOT_PASSWORD`? Copia la línea de `avisos.php` que guarda el aviso y explica qué podría pasar si concatenaras `$_POST['producto']` en la consulta, con un ejemplo de valor malicioso. Copia el `SHOW GRANTS` de `novaharvest_app` y justifica cada privilegio que tiene y alguno que no tiene.
9. **Seguridad.** Haz una tabla con cada medida de seguridad de tu proyecto (puertos, `.env`, Adminer, usuario de los procesos, usuario de la base de datos, HTTPS, `display_errors`, `client_max_body_size`...), el riesgo que evita y la **evidencia** (comando y salida) de que funciona. ¿Por qué avisa el navegador del certificado y qué haría falta para que no lo hiciera? Para `db` y abre `estado.php` con `display_errors = On`: ¿qué ve el visitante que no debería ver?
10. **Pruebas y diagnóstico.** Para `php` y pide `/estado.php` y `/`: copia el código de cada respuesta y cuánto tarda, y explica la diferencia. ¿Por qué con `php` parado sale tu `50x.html` y con `db` parada (y `display_errors = Off`) no? Observa `docker compose ps` justo después de `docker compose up -d`: ¿por qué estados pasa `db` y qué hace mientras tanto `php`? ¿Qué pasaría sin `condition: service_healthy`? Cuenta un error real que hayas tenido en el proyecto: síntoma, herramienta con la que lo diagnosticaste y solución.
11. **Documentación.** Para tu entorno, clona tu repositorio en otra carpeta y sigue **solo** tu `README.md` hasta tener NovaHarvest funcionando: copia los comandos y sus salidas. ¿Qué faltaba o has tenido que corregir? ¿Por qué se versiona `.env.example` y no `.env` ni los certificados? ¿Qué pasos del arranque no se pueden automatizar con lo visto en la UT1 y por eso tienen que estar documentados?

---

## 6. Comprobaciones

Antes de entregar, ejecuta estas comprobaciones **en orden**, en un clon limpio del repositorio. El proyecto se corregirá con **esta misma tabla**: cada comprobación se marca como superada o no, y cuenta en el CE de la última columna (ver apartado 9).

### Repositorio y configuración

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C1 | `git ls-files` | Coincide **exactamente** con la estructura del apartado 1. No aparecen `.env` ni `nginx/certs/` | i |
| C2 | `git log --oneline` | HAy commits con mensajes que describen el cambio, entre ellos `UT1 Fase 1:` a `UT1 Fase 4:` | i |
| C3 | `git log --all --oneline -- .env nginx/certs` y `git check-ignore -v .env` | El primero no muestra nada: nunca se han subido. El segundo muestra la regla del `.gitignore` | g |
| C4 | Revisar `compose.yaml` y `php/Dockerfile` | Las imágenes y etiquetas del apartado 3.1, ninguna `latest` ni sin etiqueta. El `Dockerfile` parte de `php:8.3-fpm-alpine` e instala `pdo_mysql` y `mysqli` | a |
| C5 | Revisar `nginx/default.conf` | Cumple los 8 puntos del apartado 3.3 | c |
| C6 | Revisar `php/www.conf` y `php/php.ini` | Cumplen el apartado 3.4 | e |
| C7 | Revisar `db/novaharvest.sql` | Tablas, columnas, datos y privilegios del apartado 3.5 | f |
| C8 | Revisar los `.php` de `web/` | Credenciales solo con `getenv()`. `avisos.php` usa `prepare()` y `execute()` con parámetros con nombre. Solo el PHP permitido en *Normas generales* | f |
| C9 | Revisar los ficheros de configuración | Cada bloque tiene un comentario breve que explica para qué sirve | i |

### Arranque

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C10 | Seguir el apartado **Arranque** del `README.md` | Todo funciona **a la primera**, sin pasos que no estén escritos | i |
| C11 | `docker compose config` | Sin errores, con las variables de `.env` ya sustituidas | h |
| C12 | `docker compose ps` | Cuatro servicios en ejecución y `db` `(healthy)`. `web` publica `8080->80` y `8443->443`; `adminer`, `127.0.0.1:8081->8080`; `php` y `db`, ninguno | g |
| C13 | `docker compose exec php php -m` | Aparecen `pdo_mysql` y `mysqli` | e |
| C14 | `docker compose exec php env \| grep DB_` | `DB_HOST=db`, `DB_NAME`, `DB_USER` y `DB_PASS`. **No** aparece la contraseña de `root` | g |

### Web

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C15 | `curl -I http://localhost:8080/excedentes.php` | `301`, con `Location: https://localhost:8443/excedentes.php` | g |
| C16 | Abrir `https://localhost:8443/` y recorrer el menú | Todas las páginas cargan con estilos, logotipo y la misma cabecera, menú y pie | c |
| C17 | Ver el código fuente de cada página | Cumple los 6 puntos del apartado 4.2. En las páginas `.php` no aparece código PHP | d |
| C18 | Abrir `https://localhost:8443/nada` | `404`, con tu `404.html` | c |
| C19 | `curl -kI https://localhost:8443/temporada.md` y abrirla en el navegador | `Content-Type: text/plain; charset=utf-8`. Las tildes y eñes se ven bien | c |
| C20 | `curl -kI -H "Accept-Encoding: gzip" https://localhost:8443/css/estilos.css` | Aparece `Content-Encoding: gzip` | c |
| C21 | Abrir `/estado.php` y recargar varias veces | Fecha y hora de España con el formato del apartado 4.5, que cambia en cada recarga. `Contenedor PHP:` coincide con el `CONTAINER ID` de `php` en `docker ps`. Aparece la versión de MariaDB | e |
| C22 | Abrir `/excedentes.php` | Las 4 filas con kilos, sin el calabacín, y los precios con el formato `2,24 €` | f |
| C23 | Enviar el formulario de `/avisos.html` y mirar la tabla `avisos` en Adminer | La página del apartado 4.4, y el aviso guardado con su `fecha` | f |
| C24 | `curl -k -i -X POST -d "nombre=Ana" https://localhost:8443/avisos.html` | `405` | d |
| C25 | `head -c 100K /dev/zero > /tmp/grande.bin` y `curl -k -i -X POST --data-binary @/tmp/grande.bin https://localhost:8443/avisos.php` | `413` | g |

### Base de datos

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C26 | `docker compose exec db mariadb -u root -p -e "SHOW GRANTS FOR 'novaharvest_app'@'%';"` | Solo `USAGE` y `SELECT, INSERT` sobre `novaharvest.*` | g |
| C27 | Entrar en Adminer (`http://localhost:8081`) como `novaharvest_app`, servidor `db`, y ejecutar `DELETE FROM avisos` | Ve las tablas `excedentes` y `avisos`. El `DELETE` se rechaza por falta de privilegios | h |
| C28 | `docker compose down`, `docker compose up -d` y volver a Adminer | Los avisos siguen ahí | c |

### Fallos controlados

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C29 | `docker compose stop php` y abrir `/estado.php` y `/` | `/estado.php` responde con un error `5xx` (puede tardar hasta un minuto) y se ve tu `50x.html`; `/` sigue respondiendo `200` | h |
| C30 | `docker compose start php` y abrir `/estado.php` | Todo vuelve a funcionar | h |
| C31 | `docker compose exec php ps aux` | El proceso *master* corre como `root` y los *workers* (`pool www`) como `www-data` | g |

### Memoria

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C32 | Leer el `README.md` | Tiene los 4 apartados del apartado 5, con su contenido | i |

### Fases

| # | Acción | Resultado esperado | CE |
|---|---|---|---|
| C33 | Volver al commit de cada fase y levantarlo | Cada fase funciona como describe el apartado 2 | i |

> Así se trabaja en entornos reales: los commits deben representar un cambio que funciona y, a poder ser, aislado del resto.

---

## 7. Orden de trabajo sugerido

Cada paso termina, como mínimo, en un commit. **Por ejemplo**:

| Paso | Tarea |
|---|---|
| 1 | Repositorio y `.gitignore` |
| 2 | `compose.yaml` con `web` y las páginas estáticas |
| 3 | **Fase 1**: `default.conf` con páginas de error, `gzip` y `.md` |
| 4 | Imagen de PHP-FPM: `Dockerfile`, `www.conf` y `php.ini` |
| 5 | **Fase 2**: `location` de PHP y `estado.php` |
| 6 | `.env`, `.env.example`, servicio `db` y `novaharvest.sql` cargado |
| 7 | **Fase 3**: `excedentes.php`, avisos y la base de datos en `estado.php` |
| 8 | `healthcheck` y Adminer |
| 9 | **Fase 4**: HTTPS, `client_max_body_size` y `display_errors = Off` |
| 10 | `README.md` y `cuestiones-ut1.md` |

Antes de tocar nada cuando algo falle, **mira los logs**: `docker compose ps` y `docker compose logs` lo explican casi siempre.

---

## 8. Entrega

- Debéis incluirme como colaborador en el repositorio, tenéis mi GitHub en el Moodle.

---

## 9. Rúbrica de evaluación (RA1)

Este proyecto evalúa el **RA1**: *Prepara el entorno de desarrollo y los servidores de aplicaciones Web instalando e integrando las funcionalidades necesarias.*

Cada CE se evalúa con dos tipos de evidencia:

- **Comprobaciones** (C1 a C33, apartado 6): superada o no superada.
- **Preguntas** (P1 a P11, apartado 5.1): se valora la respuesta.

### 9.1. Pasos

La nota de cada CE se calcula en tres pasos, con la misma regla para todos.

**Paso 1. Nota de cada pregunta.**

| Nota | ¿Qué responde? | ¿En qué se apoya? |
|---|---|---|
| 0 | Nada, o es incorrecta | - |
| 3 | Menos de la mitad de lo que se pregunta | - |
| 5 | Casi todo, pero le falta alguna parte | Genérica |
| 6 | Casi todo, pero le falta alguna parte | Cita su proyecto |
| 7 | Casi todo, pero le falta alguna parte | Con evidencias reales |
| 8 | Todo lo que se pregunta | Genérica |
| 9 | Todo lo que se pregunta | Cita su proyecto |
| 10 | Todo lo que se pregunta | Con evidencias reales |

- **Genérica**: valdría igual para cualquier otro proyecto.
- **Cita su proyecto**: nombra **sus** servicios, ficheros, directivas, puertos o tablas.
- **Con evidencias reales**: además, incluye configuración, comandos y salidas de **su** proyecto (`curl`, `docker compose ps`, logs, `SHOW GRANTS`, fragmentos de `default.conf`...).

**Paso 2. Nota de las preguntas del CE.** Es la media de sus preguntas. Si el CE tiene una sola pregunta, es la nota de esa pregunta.

**Paso 3. Comprobaciones del CE.** Si las supera todas (o el CE no tiene), la nota del CE es la de sus preguntas. Si falla alguna, es la de sus preguntas con un **máximo de 4**.

> **Ejemplo.** CE **c)**: P4 = 8 y P5 = 7, así que las preguntas valen 7,5. Si supera C5, C16, C18, C19, C20 y C28, el CE vale **7,5**; si falla C20, vale **4**.

### 9.2. Criterios

| CE | Comprobaciones | Preguntas | Nota |
|---|---|---|---|
| **a)** Se ha identificado el software necesario para su funcionamiento | C4 | P1 | |
| **b)** Se han identificado las diferentes tecnologías empleadas | - | P2 | |
| **c)** Se han instalado y configurado servidores Web y de bases de datos | C5, C16, C18, C19, C20, C28 | P4, P5 | |
| **d)** Se han reconocido las posibilidades de procesamiento en los entornos cliente y servidor | C17, C24 | P3 | |
| **e)** Se han añadido y configurado los componentes y módulos necesarios para el procesamiento de código en el servidor | C6, C13, C21 | P6, P7 | |
| **f)** Se ha instalado y configurado el acceso a bases de datos | C7, C8, C22, C23 | P8 | |
| **g)** Se ha establecido y verificado la seguridad en los accesos al servidor | C3, C12, C14, C15, C25, C26, C31 | P9 | |
| **h)** Se han utilizado plataformas integradas orientadas a la prueba y desarrollo de aplicaciones Web | C11, C27, C29, C30 | P10 | |
| **i)** Se han documentado los procedimientos realizados | C1, C2, C9, C10, C32, C33 | P11 | |

**Nota del RA1** = media de las notas de los CE (todos valen igual).

### 9.3. Normas de corrección

- Una comprobación que se basa en algo no visto en clase cuenta como **no superada**.
- Las comprobaciones se ejecutan con la estructura, los nombres, los puertos y las rutas del enunciado; si no se respetan, la comprobación cuenta como **no superada**.
- **Autoría**: si el alumno no sabe explicar su configuración o su código, la calificación del proyecto es **0** (ver *Normas generales*).