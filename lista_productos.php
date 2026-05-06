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
require_once("class/precios.php");

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php"> Dashboard</a>
            <a href="clientes.php">Clientes</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="categorias.php"> Categorías</a>
            <a class="activo" href="lista_productos.php">Productos</a>
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
        <h1>Lista de Productos</h1>

        <div class="barra-superior">
            <a href="agregar_producto.php" class="btn-nuevo"> Nuevo Producto</a>
            <form method="get" action="" class="form-buscar">
                <div class="buscar-fila">
                    <input type="text" name="buscar" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>
        </div>

            <?php if (!empty($busqueda)): ?>
                <p style="margin-top: 10px;">
                    Resultados para: <strong><?php echo htmlspecialchars($busqueda); ?></strong>
                    <a href="lista_productos.php">[Limpiar]</a>
                </p>
            <?php endif; ?>

        <?php
        $con = DB::conectar();
        
        if (!empty($busqueda)) {
            $sql = "SELECT p.*, c.nombre_categoria 
                    FROM productos p 
                    JOIN categorias_productos c ON p.categoria_productoid = c.id 
                    WHERE p.nombre_producto LIKE ? 
                    ORDER BY c.nombre_categoria ASC, p.nombre_producto ASC";
            $stmt = $con->prepare($sql);
            $param = "%" . $busqueda . "%";
            $stmt->bind_param("s", $param);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Producto</th><th>Categoría</th><th>Stock</th><th>Precio</th><th>Acciones</th></tr>";                
                while ($prod = $resultado->fetch_assoc()) {
                    $precio_obj = new precios();
                    $precio_obj->setProductoid($prod['id']);
                    $precio_data = $precio_obj->obtenerPorProductoId();
                    $precio_mostrar = $precio_data ? $precio_data['precio'] : '0';
                    
                    echo "<tr>";
                    echo "<td>" . $prod['nombre_producto'] . "</td>";
                    echo "<td>" . $prod['nombre_categoria'] . "</td>";
                    echo "<td>" . $prod['stock'] . "</td>";
                    echo "<td>$" . $precio_mostrar . "</td>";
                    echo "<td>";
                    echo "<a href='agregar_producto.php?editar=" . $prod['id'] . "'>Editar</a> ";
                    echo "<a href='procesar.php?eliminar=" . $prod['id'] . "' onclick='return confirm(\"Eliminar producto?\")'>Eliminar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No se encontraron productos.</p>";
            }
            $stmt->close();
        } else {
            $sql = "SELECT * FROM categorias_productos ORDER BY nombre_categoria ASC";
            $categorias = $con->query($sql);
            $hay_productos = false;
            
            if ($categorias->num_rows > 0) {
                while ($cat = $categorias->fetch_assoc()) {
                    $sql = "SELECT * FROM productos WHERE categoria_productoid = ? ORDER BY nombre_producto ASC";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $cat['id']);
                    $stmt->execute();
                    $productos_cat = $stmt->get_result();
                    
                    if ($productos_cat->num_rows > 0) {
                        $hay_productos = true;
                        echo "<h3>" . $cat['nombre_categoria'] . "</h3>";
                        echo "<table>";
                        echo "<tr><th>Producto</th><th>Stock</th><th>Precio</th><th>Acciones</th></tr>";
                        
                        while ($prod = $productos_cat->fetch_assoc()) {
                            $precio_obj = new precios();
                            $precio_obj->setProductoid($prod['id']);
                            $precio_data = $precio_obj->obtenerPorProductoId();
                            $precio_mostrar = $precio_data ? $precio_data['precio'] : '0';
                            
                            echo "<tr>";
                            echo "<td>" . $prod['nombre_producto'] . "</td>";
                            echo "<td>" . $prod['stock'] . "</td>";
                            echo "<td>$" . $precio_mostrar . "</td>";
                            echo "<td>";
                            echo "<a href='agregar_producto.php?editar=" . $prod['id'] . "'>Editar</a> ";
                            echo "<a href='procesar.php?eliminar=" . $prod['id'] . "' onclick='return confirm(\"Eliminar producto?\")'>Eliminar</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                    $stmt->close();
                }
            }
            if (!$hay_productos) echo "<p>No hay productos registrados.</p>";
        }
        ?>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>