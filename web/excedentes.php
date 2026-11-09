
<?php
// Obtener los datos de conexión desde las variables de entorno
$host = getenv('DB_HOST');
$nombreBD = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$contrasena = getenv('DB_PASS');

// Crear la conexión con MariaDB
$conexion = new PDO(
    "mysql:host=$host;dbname=$nombreBD;charset=utf8mb4",
    $usuario,
    $contrasena
);

// Consultar los productos que tienen kilos disponibles
$consulta = $conexion->query(
    "SELECT producto, productor, pueblo, kilos, precio_kg
     FROM excedentes
     WHERE kilos > 0"
);

// Obtener los resultados
$productos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaHarvest - Excedentes</title>
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
        <h1>Productos excedentes</h1>

        <p>Estos son los productos disponibles en nuestra cooperativa.</p>

        <?php foreach ($productos as $producto): ?>

            <article>
                <h2><?= $producto['producto'] ?></h2>

                <p>Productor: <?= $producto['productor'] ?></p>
                <p>Pueblo: <?= $producto['pueblo'] ?></p>
                <p>Kilos disponibles: <?= $producto['kilos'] ?></p>
                <p>Precio por kilo:
                    <?= number_format($producto['precio_kg'], 2, ',', '.') ?> €
                </p>
            </article>

        <?php endforeach; ?>

    </main>

    <footer>
        <p>NovaHarvest · IES Valle del Jerte · Curso 2026-2027</p>
    </footer>

</body>
</html>
