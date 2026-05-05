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
require_once("class/usuario.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
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
            <a href="categorias.php">Categorías</a>
            <a href="lista_productos.php">Productos</a>
            <a class="activo" href="usuarios.php"> Usuarios</a>
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
        <h1>Usuarios</h1>

        <a class="btn-nuevo" href="registro.php"> Registrar caja</a>

        <?php
        $usuario = new usuarios();
        $usuarios = $usuario->obtenerTodos();

        if ($usuarios->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Usuario</th><th>Rol</th><th>Acción</th></tr>";

            while ($user = $usuarios->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['usuario']) . "</td>";
                echo "<td>" . ($user['rol'] == 1 ? '<span style="color:#2563eb; font-weight:bold;">Admin</span>' : 'Vendedor') . "</td>";
                echo "<td>";
                if ($user['id'] != $_SESSION['usuario_id']) {
                    if ($user['rol'] == 1) {
                        echo "<a href='procesar.php?cambiar_rol=" . $user['id'] . "&rol=0' onclick='return confirm(\"¿Pasar a Vendedor?\")'>Pasar a Vendedor</a>";
                    } else {
                        echo "<a href='procesar.php?cambiar_rol=" . $user['id'] . "&rol=1' onclick='return confirm(\"¿Pasar a Admin?\")'>Pasar a Admin</a>";
                    }
                } else {
                    echo "Tú";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>

</body>
</html>