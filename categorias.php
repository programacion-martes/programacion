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

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías</title>
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
            <a class="activo" href="categorias.php"> Categorías</a>
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
        <h1>Categorías</h1>

        <div class="barra-superior">
            <a href="agregar_categoria.php" class="btn-nuevo"> Nueva Categoría</a>
            
            <form method="get" action="" class="form-buscar">
                <div class="buscar-fila">
                    <input type="text" name="buscar" placeholder="Buscar categoría..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>
        </div>

        <?php if (!empty($busqueda)): ?>
            <p>
                Resultados para: <strong><?php echo htmlspecialchars($busqueda); ?></strong>
                <a href="categorias.php">[Limpiar]</a>
            </p>
        <?php endif; ?>

        <?php
        $con = DB::conectar();
        
        if (!empty($busqueda)) {
            $sql = "SELECT * FROM categorias_productos WHERE nombre_categoria LIKE ? ORDER BY nombre_categoria ASC";
            $stmt = $con->prepare($sql);
            $param = "%" . $busqueda . "%";
            $stmt->bind_param("s", $param);
            $stmt->execute();
            $categorias = $stmt->get_result();
        } else {
            $sql = "SELECT * FROM categorias_productos ORDER BY nombre_categoria ASC";
            $categorias = $con->query($sql);
        }

        if ($categorias->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Categoría</th><th>Productos</th><th>Acciones</th></tr>";

            while ($cat = $categorias->fetch_assoc()) {
                $sql_count = "SELECT COUNT(*) as total FROM productos WHERE categoria_productoid = ?";
                $stmt_count = $con->prepare($sql_count);
                $stmt_count->bind_param("i", $cat['id']);
                $stmt_count->execute();
                $resultado_count = $stmt_count->get_result();
                $total = $resultado_count->fetch_assoc();
                $stmt_count->close();

                echo "<tr>";
                echo "<td>" . $cat['nombre_categoria'] . "</td>";
                echo "<td>" . $total['total'] . "</td>";
                echo "<td>";
                echo "<a href='agregar_categoria.php?editar=" . $cat['id'] . "'>Editar</a> ";
                echo "<a href='procesar.php?eliminar_categoria=" . $cat['id'] . "' onclick='return confirm(\"¿Eliminar categoría?\")'>Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay categorías registradas.</p>";
        }

        if (!empty($busqueda)) {
            $stmt->close();
        }
        ?>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>