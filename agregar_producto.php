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
require_once("class/productos.php");
require_once("class/categorias_productos.php");
require_once("class/precios.php");

$producto_editar = null;
$categoria_id_editar = '';
$precio_editar = '';
$iva_editar = '';
$stock_editar = '';

if (isset($_GET['editar'])) {
    $prod = new producto();
    $prod->setId($_GET['editar']);
    $producto_editar = $prod->obtenerPorId();

    if ($producto_editar) {
        $categoria_id_editar = $producto_editar['categoria_productoid'];
        $stock_editar = $producto_editar['stock'];

        $precio_obj = new precios();
        $precio_obj->setProductoid($_GET['editar']);
        $precio_data = $precio_obj->obtenerPorProductoId();
        if ($precio_data) {
            $precio_editar = $precio_data['precio'];
            $iva_editar = $precio_data['iva'];
        }
    }
}

$con = DB::conectar();
$sql_categorias = "SELECT * FROM categorias_productos ORDER BY nombre_categoria ASC";
$categorias = $con->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $producto_editar ? 'Editar' : 'Agregar'; ?> Producto</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php"> Dashboard</a>
            <a href="agregar_producto.php">Agregar Producto</a>
            <a href="lista_productos.php"> Lista de Productos</a>
            <a href="categorias.php"> Categorías</a>
            <a href="clientes.php"> Clientes</a>
            <a href="usuarios.php"> Usuarios</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
        <?php else: ?>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="clientes.php">👥 Clientes</a>
        <?php endif; ?>
    </nav>
    <div class="logout-link">
        <a href="logout.php">🚪 Cerrar Sesión</a>
    </div>
</div>

    <div class="main">
        <h1><?php echo $producto_editar ? 'Editar Producto' : 'Agregar Producto'; ?></h1>

        <form action="procesar.php" method="post">
            <?php if ($producto_editar): ?>
                <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
            <?php endif; ?>

            <label>Categoría:</label>
            <select name="categoria_id" required>
                <option value="">Seleccionar categoría...</option>
                <?php
                if ($categorias->num_rows > 0) {
                    while ($cat = $categorias->fetch_assoc()) {
                        $seleccionada = ($categoria_id_editar == $cat['id']) ? 'selected' : '';
                        echo "<option value='" . $cat['id'] . "' $seleccionada>" . $cat['nombre_categoria'] . "</option>";
                    }
                }
                ?>
            </select>

            <label>Producto:</label>
            <input type="text" name="nombre_producto" value="<?php echo $producto_editar ? $producto_editar['nombre_producto'] : ''; ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo $producto_editar ? $stock_editar : '0'; ?>" min="0" required>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?php echo $precio_editar; ?>" required>

            <label>IVA (%):</label>
            <input type="number" step="0.01" name="iva" value="<?php echo $iva_editar ? $iva_editar : '16'; ?>" required>

            <?php if ($producto_editar): ?>
                <button type="submit" name="actualizar_producto">Actualizar</button>
            <?php else: ?>
                <button type="submit" name="guardar_producto">Guardar</button>
            <?php endif; ?>
        </form>
    </div>
    
    <script src="assets/js/validaciones.js"></script>
</body>
</html>