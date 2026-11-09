
<?php
// Obtener los datos de conexión
$host = getenv('DB_HOST');
$nombreBD = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$contrasena = getenv('DB_PASS');

// Conectar con MariaDB
$conexion = new PDO(
    "mysql:host=$host;dbname=$nombreBD;charset=utf8mb4",
    $usuario,
    $contrasena
);

// Preparar la consulta para guardar el aviso
$consulta = $conexion->prepare(
    "INSERT INTO avisos (nombre, email, producto)
     VALUES (:nombre, :email, :producto)"
);

// Ejecutar la consulta con los datos del formulario
$consulta->execute([
    ':nombre' => $_POST['nombre'],
    ':email' => $_POST['email'],
    ':producto' => $_POST['producto']
]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaHarvest - Aviso registrado</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <img src="img/logo.png" alt="Logo de NovaHarvest">
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
        <h1>¡Solicitud registrada!</h1>

        <p>Tu solicitud se ha guardado correctamente.</p>
        <p>Gracias por confiar en NovaHarvest.</p>

        <a href="/">Volver al inicio</a>
    </main>

    <footer>
        <p>NovaHarvest · IES Valle del Jerte · Curso 2026-2027</p>
    </footer>

</body>
</html>
