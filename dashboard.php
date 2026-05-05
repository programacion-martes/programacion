<?php
session_start();
require_once("class/usuario.php");
require_once("class/productos.php");
require_once("class/categorias_productos.php");
require_once("class/precios.php");
require_once("class/cliente.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// EDITAR PRODUCTO
$producto_editar = null;
$nombre_categoria_editar = '';
$precio_editar = '';
$iva_editar = '';
if (isset($_GET['editar'])) {
    $prod = new producto();
    $prod->setId($_GET['editar']);
    $producto_editar = $prod->obtenerPorId();
    
    if ($producto_editar) {
        $con = DB::conectar();
        $sql = "SELECT nombre_categoria FROM categorias_productos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $producto_editar['categoria_productoid']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $cat = $resultado->fetch_assoc();
        $nombre_categoria_editar = $cat['nombre_categoria'];
        $stmt->close();
        
        $precio_obj = new precios();
        $precio_obj->setProductoid($_GET['editar']);
        $precio_data = $precio_obj->obtenerPorProductoId();
        if ($precio_data) {
            $precio_editar = $precio_data['precio'];
            $iva_editar = $precio_data['iva'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Ventas</title>
</head>
<body>
    <h1>Bienvenido <?php echo $_SESSION['usuario_nombre']; ?></h1>
    <a href="logout.php">Cerrar Sesión</a>
    
    <h2><?php echo $producto_editar ? 'Editar Producto' : 'Agregar Producto'; ?></h2>
    <form action="procesar.php" method="post">
        <?php if ($producto_editar): ?>
            <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
        <?php endif; ?>
        <label>Categoría:</label><br>
        <input type="text" name="categoria" value="<?php echo $nombre_categoria_editar; ?>" required><br><br>
        <label>Producto:</label><br>
        <input type="text" name="nombre_producto" value="<?php echo $producto_editar ? $producto_editar['nombre_producto'] : ''; ?>" required><br><br>
        <label>Precio:</label><br>
        <input type="number" step="0.01" name="precio" value="<?php echo $precio_editar; ?>" required><br><br>
        <label>IVA (%):</label><br>
        <input type="number" step="0.01" name="iva" value="<?php echo $iva_editar ? $iva_editar : '16'; ?>" required><br><br>
        <?php if ($producto_editar): ?>
            <button type="submit" name="actualizar_producto">Actualizar</button>
            <a href="dashboard.php">Cancelar</a>
        <?php else: ?>
            <button type="submit" name="guardar_producto">Guardar</button>
        <?php endif; ?>
    </form>
    
    <hr>
    
    <h2>Registrar Venta</h2>
    <form action="procesar.php" method="post">
        <label>Documento (V/E/J):</label><br>
        <input type="text" name="documento" maxlength="1" required><br><br>
        <label>Número de Documento:</label><br>
        <input type="text" name="numerodocumento" required><br><br>
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>
        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br><br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" required><br><br>
        <label>Dirección:</label><br>
        <input type="text" name="direccion" required><br><br>
        
        <h3>Productos:</h3>
        <?php
        $con = DB::conectar();
        $sql = "SELECT p.id, p.nombre_producto, pr.precio, pr.iva 
                FROM productos p 
                JOIN precios pr ON p.id = pr.productoid 
                ORDER BY p.nombre_producto ASC";
        $productos_lista = $con->query($sql);
        
        if ($productos_lista->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Producto</th><th>Precio</th><th>IVA</th><th>Total</th><th>Cantidad</th></tr>";
            while ($prod = $productos_lista->fetch_assoc()) {
                $total_prod = $prod['precio'] + ($prod['precio'] * $prod['iva'] / 100);
                echo "<tr>";
                echo "<td>" . $prod['nombre_producto'] . "</td>";
                echo "<td>$" . $prod['precio'] . "</td>";
                echo "<td>" . $prod['iva'] . "%</td>";
                echo "<td>$" . number_format($total_prod, 2) . "</td>";
                echo "<td><input type='number' name='cantidad[" . $prod['id'] . "]' value='0' min='0' style='width:60px'></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        <br>
        <button type="submit" name="guardar_venta">Registrar Venta</button>
    </form>
    
    <hr>
    
    <h2>Lista de Productos</h2>
    <?php
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
                echo "<table border='1'>";
                echo "<tr><th>Producto</th><th>Precio</th><th>IVA</th><th>Total</th><th>Acciones</th></tr>";
                
                while ($prod = $productos_cat->fetch_assoc()) {
                    $precio_obj = new precios();
                    $precio_obj->setProductoid($prod['id']);
                    $precio_data = $precio_obj->obtenerPorProductoId();
                    $precio_mostrar = $precio_data ? $precio_data['precio'] : '0';
                    $iva_mostrar = $precio_data ? $precio_data['iva'] : '0';
                    $total = $precio_mostrar + ($precio_mostrar * $iva_mostrar / 100);
                    
                    echo "<tr>";
                    echo "<td>" . $prod['nombre_producto'] . "</td>";
                    echo "<td>$" . $precio_mostrar . "</td>";
                    echo "<td>" . $iva_mostrar . "%</td>";
                    echo "<td>$" . number_format($total, 2) . "</td>";
                    echo "<td>";
                    echo "<a href='dashboard.php?editar=" . $prod['id'] . "'>Editar</a> ";
                    echo "<a href='procesar.php?eliminar=" . $prod['id'] . "'>Eliminar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table><br>";
            }
            $stmt->close();
        }
    }
    if (!$hay_productos) echo "No hay productos registrados.";
    ?>
    
    <hr>
    
    <h2>Ventas Realizadas</h2>
    <?php
    $sql = "SELECT v.id as venta_id, v.fecha, c.nombre, c.apellido, c.numerodocumento 
            FROM ventas v 
            JOIN clientes c ON v.clienteid = c.id 
            ORDER BY v.fecha DESC";
    $ventas = $con->query($sql);
    
    if ($ventas->num_rows > 0) {
        while ($venta = $ventas->fetch_assoc()) {
            echo "<h3>Venta #" . $venta['venta_id'] . " - " . $venta['fecha'] . " - " . $venta['nombre'] . " " . $venta['apellido'] . " (C.I: " . $venta['numerodocumento'] . ")</h3>";
            
            $sql_det = "SELECT p.nombre_producto, pr.precio, pr.iva, COUNT(*) as cantidad 
                        FROM detalles_ventas dv 
                        JOIN precios pr ON dv.precioid = pr.id 
                        JOIN productos p ON pr.productoid = p.id 
                        WHERE dv.ventaid = ? 
                        GROUP BY pr.id 
                        ORDER BY p.nombre_producto ASC";
            $stmt_det = $con->prepare($sql_det);
            $stmt_det->bind_param("i", $venta['venta_id']);
            $stmt_det->execute();
            $detalles = $stmt_det->get_result();
            
            echo "<table border='1'>";
            echo "<tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>IVA</th><th>Total Unit.</th><th>Subtotal</th></tr>";
            
            $total_sin_iva = 0;
            $total_con_iva = 0;
            
            while ($det = $detalles->fetch_assoc()) {
                $total_unit = $det['precio'] + ($det['precio'] * $det['iva'] / 100);
                $subtotal = $total_unit * $det['cantidad'];
                $total_sin_iva += $det['precio'] * $det['cantidad'];
                $total_con_iva += $subtotal;
                
                echo "<tr>";
                echo "<td>" . $det['nombre_producto'] . "</td>";
                echo "<td>" . $det['cantidad'] . "</td>";
                echo "<td>$" . $det['precio'] . "</td>";
                echo "<td>" . $det['iva'] . "%</td>";
                echo "<td>$" . number_format($total_unit, 2) . "</td>";
                echo "<td>$" . number_format($subtotal, 2) . "</td>";
                echo "</tr>";
            }
            
            echo "<tr><td colspan='5'><b>Total sin IVA</b></td><td><b>$" . number_format($total_sin_iva, 2) . "</b></td></tr>";
            echo "<tr><td colspan='5'><b>Total con IVA</b></td><td><b>$" . number_format($total_con_iva, 2) . "</b></td></tr>";
            echo "</table><br>";
            
            $stmt_det->close();
        }
    } else {
        echo "No hay ventas registradas.";
    }
    ?>
</body>
</html>