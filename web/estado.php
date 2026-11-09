<?php
// Obtenemos la fecha y hora actual
$fecha = date("d/m/Y H:i:s");

// Obtenemos el nombre del contenedor PHP
$servidor = getenv('HOSTNAME');

// Datos de conexión a MariaDB
$host = getenv('DB_HOST');
$nombreBD = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$contrasena = getenv('DB_PASS');

// Conectamos con la base de datos
$conexion = new PDO(
    "mysql:host=$host;dbname=$nombreBD;charset=utf8mb4",
    $usuario,
    $contrasena
);

// Consultamos la versión de MariaDB
$consulta = $conexion->query("SELECT VERSION()");
$version = $consulta->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado - NovaHarvest</title>
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<body>

<header>
    <h1>NovaHarvest</h1>
    <nav>
        <a href="/">Inicio</a>
        <a href="/excedentes.php">Excedentes</a>
        <a href="/avisos.html">Avísame</a>
        <a href="/temporada.md">Temporada</a>
        <a href="/estado.php">Estado</a>
    </nav>
</header>

<main>
    <h2>Estado del sistema</h2>

    <p>Fecha y hora: <?= $fecha ?></p>
    <p>Servidor PHP: <?= $servidor ?></p>
    <p>Versión de MariaDB: <?= $version ?></p>
    <p>Página generada por PHP-FPM</p>
</main>

<footer>
    NovaHarvest · IES Valle del Jerte · Curso 2026-2027
</footer>

</body>
</html>