<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("class/DB.php");
require_once("class/cliente.php");

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php"> Dashboard</a>
            <a class="activo" href="clientes.php">Clientes</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
            <a href="categorias.php"> Categorías</a>
            <a href="lista_productos.php">Productos</a>
            <a href="usuarios.php"> Usuarios</a>
        <?php else: ?>
            <a class="activo" href="clientes.php"> Clientes</a>
            <a href="registrar_venta.php"> Registrar Venta</a>
        <?php endif; ?>
    </nav>
    <div class="logout-link">
        <a href="logout.php"> Cerrar Sesión</a>
    </div>
</div>
    <div class="main">
        <h1>Clientes</h1>

        <div class="barra-superior">
            <a href="agregar_cliente.php" class="btn-nuevo"> Nuevo Cliente</a>          
            
            <form method="get" action="" class="form-buscar">
                <div class="buscar-fila">
                    <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o cédula..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>
        </div>

        <?php if (!empty($busqueda)): ?>
            <p>
                Resultados para: <strong><?php echo htmlspecialchars($busqueda); ?></strong>
                <a href="clientes.php">[Limpiar]</a>
            </p>
        <?php endif; ?>

        <?php
        $con = DB::conectar();
        
        if (!empty($busqueda)) {
            $sql = "SELECT * FROM clientes 
                    WHERE nombre LIKE ? OR apellido LIKE ? OR numerodocumento LIKE ? 
                    ORDER BY apellido ASC, nombre ASC";
            $stmt = $con->prepare($sql);
            $param = "%" . $busqueda . "%";
            $stmt->bind_param("sss", $param, $param, $param);
            $stmt->execute();
            $clientes = $stmt->get_result();
        } else {
            $cliente = new cliente();
            $clientes = $cliente->obtenerTodos();
        }

        if ($clientes->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Documento</th><th>Número</th><th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th></tr>";

            while ($cli = $clientes->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $cli['documento'] . "</td>";
                echo "<td>" . $cli['numerodocumento'] . "</td>";
                echo "<td>" . $cli['nombre'] . "</td>";
                echo "<td>" . $cli['apellido'] . "</td>";
                echo "<td>" . $cli['telefono'] . "</td>";
                echo "<td>" . $cli['direccion'] . "</td>";
                echo "<td>";
                echo "<a href='agregar_cliente.php?editar=" . $cli['id'] . "'>Editar</a> ";
                echo "<a href='procesar.php?eliminar_cliente=" . $cli['id'] . "' onclick='return confirm(\"¿Eliminar cliente?\")'>Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No se encontraron clientes.</p>";
        }

        if (!empty($busqueda)) {
            $stmt->close();
        }
        ?>
    </div>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>