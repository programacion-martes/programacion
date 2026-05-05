<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['usuario_rol'] != 1) {
    header("Location: registrar_venta.php");
    exit();
}
require_once("class/DB.php");
require_once("class/categorias_productos.php");

$categoria_editar = null;
if (isset($_GET['editar'])) {
    $cat = new categoria_productos();
    $cat->setId($_GET['editar']);
    $categoria_editar = $cat->obtenerPorId();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $categoria_editar ? 'Editar' : 'Agregar'; ?> Categoría</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php"> Dashboard</a>
            <a href="clientes.php"> Clientes</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="categorias.php"> Categorías</a>
            <a href="lista_productos.php">Productos</a>
            <a href="usuarios.php"> Usuarios</a>
        <?php else: ?>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="clientes.php">Clientes</a>
        <?php endif; ?>
    </nav>
    <div class="logout-link">
        <a href="logout.php"> Cerrar Sesión</a>
    </div>
</div>

    <div class="main">
        <h1><?php echo $categoria_editar ? 'Editar Categoría' : 'Agregar Categoría'; ?></h1>

        <form action="procesar.php" method="post">
            <?php if ($categoria_editar): ?>
                <input type="hidden" name="id" value="<?php echo $categoria_editar['id']; ?>">
            <?php endif; ?>

            <label>Nombre de la Categoría:</label>
            <input type="text" name="nombre_categoria" value="<?php echo $categoria_editar ? $categoria_editar['nombre_categoria'] : ''; ?>" required>

            <?php if ($categoria_editar): ?>
                <button type="submit" name="actualizar_categoria">Actualizar</button>
            <?php else: ?>
                <button type="submit" name="guardar_categoria">Guardar</button>
            <?php endif; ?>
        </form>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>