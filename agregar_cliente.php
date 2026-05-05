<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("class/DB.php");
require_once("class/cliente.php");

$cliente_editar = null;
if (isset($_GET['editar'])) {
    $cli = new cliente();
    $cli->setId($_GET['editar']);
    $cliente_editar = $cli->obtenerPorId();
}

$cedula_precargada = isset($_GET['cedula']) ? $_GET['cedula'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $cliente_editar ? 'Editar' : 'Agregar'; ?> Cliente</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="clientes.php"> Clientes</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="categorias.php"> Categorías</a>
            <a href="lista_productos.php">Productos</a>
            <a href="usuarios.php"> Usuarios</a>
        <?php else: ?>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="clientes.php"> Clientes</a>
        <?php endif; ?>
    </nav>
    <div class="logout-link">
        <a href="logout.php"> Cerrar Sesión</a>
    </div>
</div>

    <div class="main">
        <h1><?php echo $cliente_editar ? 'Editar Cliente' : 'Agregar Cliente'; ?></h1>

        <form action="procesar.php" method="post">
            <?php if ($cliente_editar): ?>
                <input type="hidden" name="id" value="<?php echo $cliente_editar['id']; ?>">
            <?php endif; ?>

            <label>Documento:</label>
            <select name="documento" required>
                <option value="V" <?php echo ($cliente_editar && $cliente_editar['documento'] == 'V') ? 'selected' : ''; ?>>V - Venezolano</option>
                <option value="E" <?php echo ($cliente_editar && $cliente_editar['documento'] == 'E') ? 'selected' : ''; ?>>E - Extranjero</option>
                <option value="J" <?php echo ($cliente_editar && $cliente_editar['documento'] == 'J') ? 'selected' : ''; ?>>J - Jurídico</option>
            </select>

            <label>Número de Documento:</label>
            <input type="text" name="numerodocumento" value="<?php echo $cliente_editar ? $cliente_editar['numerodocumento'] : $cedula_precargada; ?>" required>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo $cliente_editar ? $cliente_editar['nombre'] : ''; ?>" required>

            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?php echo $cliente_editar ? $cliente_editar['apellido'] : ''; ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo $cliente_editar ? $cliente_editar['telefono'] : ''; ?>" required>

            <label>Dirección:</label>
            <textarea name="direccion" rows="2" required><?php echo $cliente_editar ? $cliente_editar['direccion'] : ''; ?></textarea>

            <?php if ($cliente_editar): ?>
                <button type="submit" name="actualizar_cliente">Actualizar Cliente</button>
            <?php else: ?>
                <button type="submit" name="guardar_cliente">Guardar Cliente</button>
            <?php endif; ?>
        </form>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>