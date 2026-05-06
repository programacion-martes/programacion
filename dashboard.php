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
require_once("class/iva.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Ventas</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a class="activo" href="dashboard.php"> Dashboard</a>
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
        <h1>Dashboard</h1>

        <?php
        $con = DB::conectar();
        
        $sql_total_vendido_hoy = "SELECT ROUND(SUM(pr.precio + (pr.precio * COALESCE(i.porcentaje, 16) / 100)), 2) as total 
                                  FROM detalles_ventas dv 
                                  JOIN precios pr ON dv.precioid = pr.id 
                                  JOIN ventas v ON dv.ventaid = v.id 
                                  LEFT JOIN iva i ON v.iva_id = i.id 
                                  WHERE DATE(v.fecha) = CURDATE()";
        $total_vendido_hoy = $con->query($sql_total_vendido_hoy)->fetch_assoc()['total'];
        $total_vendido_hoy = $total_vendido_hoy ? $total_vendido_hoy : 0;
                        
        $sql_total_hoy = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE()";
        $total_hoy = $con->query($sql_total_hoy)->fetch_assoc()['total'];
        
        $sql_total_clientes = "SELECT COUNT(*) as total FROM clientes";
        $total_clientes = $con->query($sql_total_clientes)->fetch_assoc()['total'];
        
        $sql_total_productos = "SELECT COUNT(*) as total FROM productos";
        $total_productos = $con->query($sql_total_productos)->fetch_assoc()['total'];
        ?>

        <div class="tarjetas">
            <div class="tarjeta">
                <div class="tarjeta-numero">$<?php echo $total_vendido_hoy; ?></div>
                <div class="tarjeta-texto">Total vendido hoy</div>
            </div>
            <div class="tarjeta">
                <div class="tarjeta-numero"><?php echo $total_hoy; ?></div>
                <div class="tarjeta-texto">Ventas de Hoy</div>
            </div>
            <div class="tarjeta">
                <div class="tarjeta-numero"><?php echo $total_clientes; ?></div>
                <div class="tarjeta-texto">Clientes</div>
            </div>
            <div class="tarjeta">
                <div class="tarjeta-numero"><?php echo $total_productos; ?></div>
                <div class="tarjeta-texto">Productos</div>
            </div>
        </div>

        <h2>Últimas Ventas</h2>

        <?php
        $sql = "SELECT v.id as venta_id, v.fecha, c.nombre, c.apellido, c.documento, c.numerodocumento 
                FROM ventas v 
                JOIN clientes c ON v.clienteid = c.id 
                ORDER BY v.fecha DESC 
                LIMIT 5";
        $ventas = $con->query($sql);

        if ($ventas->num_rows > 0) {
            while ($venta = $ventas->fetch_assoc()) {
                echo "<div class='venta-card'>";
                echo "<div class='venta-header'>";
                echo "<span><strong></strong></span>";
                echo "<span>" . $venta['fecha'] . "</span>";
                echo "</div>";
                echo "<div class='venta-cliente'>";
                echo $venta['nombre'] . " " . $venta['apellido'] . " — C.I: " . $venta['documento'] . "-" . $venta['numerodocumento'];
                echo "</div>";

                $sql_det = "SELECT p.nombre_producto, pr.precio, COALESCE(i.porcentaje, 16) as iva, COUNT(*) as cantidad 
                            FROM detalles_ventas dv 
                            JOIN precios pr ON dv.precioid = pr.id 
                            JOIN productos p ON pr.productoid = p.id 
                            JOIN ventas v ON dv.ventaid = v.id 
                            LEFT JOIN iva i ON v.iva_id = i.id 
                            WHERE dv.ventaid = ? 
                            GROUP BY pr.id 
                            ORDER BY p.nombre_producto ASC";
                $stmt_det = $con->prepare($sql_det);
                $stmt_det->bind_param("i", $venta['venta_id']);
                $stmt_det->execute();
                $detalles = $stmt_det->get_result();

                echo "<table>";
                echo "<tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>IVA</th><th>Total</th></tr>";

                $total_venta = 0;

                while ($det = $detalles->fetch_assoc()) {
                    $total_unit = $det['precio'] + ($det['precio'] * $det['iva'] / 100);
                    $subtotal = $total_unit * $det['cantidad'];
                    $total_venta += $subtotal;

                    echo "<tr>";
                    echo "<td>" . $det['nombre_producto'] . "</td>";
                    echo "<td>" . $det['cantidad'] . "</td>";
                    echo "<td>$" . $det['precio'] . "</td>";
                    echo "<td>" . $det['iva'] . "%</td>";
                    echo "<td>$" . number_format($subtotal, 2) . "</td>";
                    echo "</tr>";
                }

                echo "<tr class='total-row'><td colspan='4'><strong>Total</strong></td><td><strong>$" . number_format($total_venta, 2) . "</strong></td></tr>";
                echo "</table>";
                echo "</div>";

                $stmt_det->close();
            }
        } else {
            echo "<p>No hay ventas registradas.</p>";
        }
        ?>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>