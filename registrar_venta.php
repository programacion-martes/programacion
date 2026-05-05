<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("class/DB.php");
require_once("class/cliente.php");

$cliente_encontrado = null;
$mensaje = '';

if (isset($_GET['cedula']) && !empty($_GET['cedula'])) {
    $cli = new cliente();
    $cli->setNumeroDocumento($_GET['cedula']);
    
    if ($cli->existeCedula()) {
        $con = DB::conectar();
        $sql = "SELECT * FROM clientes WHERE numerodocumento = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $_GET['cedula']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $cliente_encontrado = $resultado->fetch_assoc();
        $stmt->close();
    } else {
        $mensaje = "Cliente no encontrado. <a href='agregar_cliente.php?cedula=" . $_GET['cedula'] . "'>Registrarlo aquí</a>";
    }
}

if (isset($_POST['buscar_cliente'])) {
    $cli = new cliente();
    $cli->setNumeroDocumento($_POST['buscar_cedula']);
    
    if ($cli->existeCedula()) {
        $con = DB::conectar();
        $sql = "SELECT * FROM clientes WHERE numerodocumento = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $_POST['buscar_cedula']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $cliente_encontrado = $resultado->fetch_assoc();
        $stmt->close();
    } else {
        $mensaje = "Cliente no encontrado. <a href='agregar_cliente.php?cedula=" . $_POST['buscar_cedula'] . "'>Registrarlo aquí</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Sistema POS</div>
    <nav>
        <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="clientes.php"> Clientes</a>
            <a class="activo" href="registrar_venta.php"> Registrar Venta</a>
            <a href="categorias.php"> Categorías</a>
            <a href="lista_productos.php">Productos</a>
            <a href="usuarios.php"> Usuarios</a>
        <?php else: ?>
            <a href="clientes.php"> Clientes</a>
            <a class="activo" href="registrar_venta.php">Registrar Venta</a>
        <?php endif; ?>
    </nav>
    <div class="logout-link">
        <a href="logout.php"> Cerrar Sesión</a>
    </div>
</div>
    <div class="main">
        <h1>Registrar Venta</h1>

        <?php if (!$cliente_encontrado): ?>
        <form method="post" action="" class="form-buscar">
            <label>Buscar Cliente por Cédula:</label>
            <div class="buscar-fila">
                <input type="text" name="buscar_cedula" placeholder="Ingrese número de cédula" required>
                <button type="submit" name="buscar_cliente">Buscar</button>
            </div>
            <?php if ($mensaje): ?>
                <p class="mensaje-error"><?php echo $mensaje; ?></p>
            <?php endif; ?>
        </form>
        <?php endif; ?>

        <?php if ($cliente_encontrado): ?>
        <form action="procesar.php" method="post" class="form-venta" id="form-venta">
            <input type="hidden" name="cliente_id" value="<?php echo $cliente_encontrado['id']; ?>">
            <input type="hidden" name="total_venta" id="total_venta_input" value="0">

            <div class="venta-header">
                <span><strong>Cliente:</strong> <?php echo $cliente_encontrado['nombre'] . " " . $cliente_encontrado['apellido']; ?></span>
                <a href="registrar_venta.php" class="btn-cambiar">Cambiar</a>
            </div>

            <div class="datos-cliente">
                <p><strong>Cédula:</strong> <?php echo $cliente_encontrado['documento'] . "-" . $cliente_encontrado['numerodocumento']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $cliente_encontrado['telefono']; ?></p>
                <p><strong>Dirección:</strong> <?php echo $cliente_encontrado['direccion']; ?></p>
            </div>

            <h3>Productos disponibles:</h3>

            <input type="text" id="filtro-productos" placeholder="Filtrar productos..." autocomplete="off">

            <?php
            $con = DB::conectar();
            $sql = "SELECT p.id, p.nombre_producto, p.stock, pr.precio, pr.iva 
                    FROM productos p 
                    JOIN precios pr ON p.id = pr.productoid 
                    ORDER BY p.nombre_producto ASC
                    LIMIT 5";
            $productos_lista = $con->query($sql);

            if ($productos_lista->num_rows > 0) {
                echo "<table id='tabla-productos'>";
                echo "<thead><tr><th>Producto</th><th>Stock</th><th>Precio</th><th>IVA</th><th>Total Unit.</th><th>Cantidad</th><th>Subtotal</th></tr></thead>";
                echo "<tbody>";
                while ($prod = $productos_lista->fetch_assoc()) {
                    $total_prod = $prod['precio'] + ($prod['precio'] * $prod['iva'] / 100);
                    echo "<tr class='producto-fila' data-nombre='" . htmlspecialchars($prod['nombre_producto']) . "'>";
                    echo "<td>" . $prod['nombre_producto'] . "</td>";
                    echo "<td>" . $prod['stock'] . "</td>";
                    echo "<td>$" . $prod['precio'] . "</td>";
                    echo "<td>" . $prod['iva'] . "%</td>";
                    echo "<td>$" . number_format($total_prod, 2) . "</td>";
                    echo "<td><input type='number' name='cantidad[" . $prod['id'] . "]' value='0' min='0' max='" . $prod['stock'] . "' class='cantidad-input' data-precio='" . $prod['precio'] . "' data-iva='" . $prod['iva'] . "'></td>";
                    echo "<td class='subtotal-producto'>$0.00</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";

                echo "<div class='total-venta-box'>";
                echo "<strong>Total: </strong><span id='total-venta'>$0.00</span>";
                echo "</div>";
            } else {
                echo "<p>No hay productos. Agrega uno primero.</p>";
            }
            ?>
            <button type="submit" name="guardar_venta">Registrar Venta</button>
        </form>
        <?php endif; ?>
    </div>

    <script src="assets/js/validaciones.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        let filtroInput = document.getElementById('filtro-productos');
        
        if (filtroInput) {
            filtroInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

            filtroInput.addEventListener('keyup', function() {
                let filtro = this.value.toLowerCase().trim();
                let filas = document.querySelectorAll('.producto-fila');
                
                filas.forEach(function(fila) {
                    let nombre = fila.getAttribute('data-nombre').toLowerCase();
                    fila.style.display = (filtro === '' || nombre.includes(filtro)) ? '' : 'none';
                });
            });
        }

        function calcularTotalVenta() {
            let totalGeneral = 0;
            
            document.querySelectorAll('.cantidad-input').forEach(function(input) {
                let cantidad = parseInt(input.value) || 0;
                let precio = parseFloat(input.getAttribute('data-precio'));
                let iva = parseFloat(input.getAttribute('data-iva'));
                
                if (cantidad > 0) {
                    let totalUnitario = precio + (precio * iva / 100);
                    let subtotal = totalUnitario * cantidad;
                    totalGeneral += subtotal;
                    
                    let fila = input.closest('tr');
                    let celdaSubtotal = fila.querySelector('.subtotal-producto');
                    if (celdaSubtotal) {
                        celdaSubtotal.textContent = '$' + subtotal.toFixed(2);
                    }
                } else {
                    let fila = input.closest('tr');
                    let celdaSubtotal = fila.querySelector('.subtotal-producto');
                    if (celdaSubtotal) {
                        celdaSubtotal.textContent = '$0.00';
                    }
                }
            });
            
            document.getElementById('total-venta').textContent = '$' + totalGeneral.toFixed(2);
            document.getElementById('total_venta_input').value = totalGeneral.toFixed(2);
        }

        document.querySelectorAll('.cantidad-input').forEach(function(input) {
            input.addEventListener('input', calcularTotalVenta);
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') e.preventDefault();
            });
            input.addEventListener('change', function() {
                let max = parseInt(this.getAttribute('max'));
                if (parseInt(this.value) > max) {
                    this.value = max;
                    calcularTotalVenta();
                }
            });
        });

        calcularTotalVenta();

        let formVenta = document.getElementById('form-venta');
        if (formVenta) {
            formVenta.addEventListener('submit', function(e) {
                let total = parseFloat(document.getElementById('total_venta_input').value) || 0;
                if (total <= 0) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos un producto.');
                }
            });
        }

    });
    </script>
</body>
</html>