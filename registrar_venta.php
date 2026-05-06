<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("class/DB.php");
require_once("class/cliente.php");
require_once("class/iva.php");

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

$iva_actual = iva::obtenerActual();
$porcentaje_iva = $iva_actual ? $iva_actual['porcentaje'] : 16;
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
            <input type="hidden" name="porcentaje_iva" id="porcentaje_iva_input" value="<?php echo $porcentaje_iva; ?>">

            <div class="venta-header">
                <span><strong>Cliente:</strong> <?php echo $cliente_encontrado['nombre'] . " " . $cliente_encontrado['apellido']; ?></span>
                <a href="registrar_venta.php" class="btn-cambiar">Cambiar</a>
            </div>

            <div class="datos-cliente">
                <p><strong>Cédula:</strong> <?php echo $cliente_encontrado['documento'] . "-" . $cliente_encontrado['numerodocumento']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $cliente_encontrado['telefono']; ?></p>
                <p><strong>Dirección:</strong> <?php echo $cliente_encontrado['direccion']; ?></p>
            </div>

            <div class="datos-cliente">
                <label><strong>IVA de esta venta (%):</strong></label>
                <input type="number" step="0.01" id="iva-porcentaje" value="<?php echo $porcentaje_iva; ?>" min="0" max="100" style="width:100px; display:inline-block;">
            </div>

            <h3>Productos disponibles:</h3>

            <input type="text" id="filtro-productos" placeholder="Filtrar productos..." autocomplete="off">

            <?php
            $con = DB::conectar();
            $sql = "SELECT p.id, p.nombre_producto, p.stock, pr.precio 
                    FROM productos p 
                    JOIN precios pr ON p.id = pr.productoid 
                    ORDER BY p.nombre_producto ASC";
            $productos_lista = $con->query($sql);

            if ($productos_lista->num_rows > 0) {
                echo "<table id='tabla-productos'>";
                echo "<thead><tr><th>Producto</th><th>Stock</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>";
                echo "<tbody>";
                while ($prod = $productos_lista->fetch_assoc()) {
                    echo "<tr class='producto-fila' data-nombre='" . htmlspecialchars($prod['nombre_producto']) . "'>";
                    echo "<td>" . $prod['nombre_producto'] . "</td>";
                    echo "<td>" . $prod['stock'] . "</td>";
                    echo "<td>$" . $prod['precio'] . "</td>";
                    echo "<td><input type='number' name='cantidad[" . $prod['id'] . "]' value='0' min='0' max='" . $prod['stock'] . "' class='cantidad-input' data-precio='" . $prod['precio'] . "'></td>";
                    echo "<td class='subtotal-producto'>$0.00</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";

                echo "<div class='total-venta-box'>";
                echo "<strong>Subtotal: </strong><span id='subtotal-venta'>$0.00</span><br>";
                echo "<strong>IVA (<span id='iva-porcentaje-txt'><?php echo $porcentaje_iva; ?></span>%): </strong><span id='iva-venta'>$0.00</span><br>";
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
            let subtotalGeneral = 0;
            let porcentajeIva = parseFloat(document.getElementById('iva-porcentaje').value) || 0;
            
            document.querySelectorAll('.cantidad-input').forEach(function(input) {
                let cantidad = parseInt(input.value) || 0;
                let precio = parseFloat(input.getAttribute('data-precio'));
                
                if (cantidad > 0) {
                    let subtotal = precio * cantidad;
                    subtotalGeneral += subtotal;
                    
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
            
            let ivaTotal = subtotalGeneral * porcentajeIva / 100;
            let totalGeneral = subtotalGeneral + ivaTotal;
            
            document.getElementById('iva-porcentaje-txt').textContent = porcentajeIva;
            document.getElementById('subtotal-venta').textContent = '$' + subtotalGeneral.toFixed(2);
            document.getElementById('iva-venta').textContent = '$' + ivaTotal.toFixed(2);
            document.getElementById('total-venta').textContent = '$' + totalGeneral.toFixed(2);
            document.getElementById('total_venta_input').value = totalGeneral.toFixed(2);
            document.getElementById('porcentaje_iva_input').value = porcentajeIva;
        }

        document.getElementById('iva-porcentaje').addEventListener('input', calcularTotalVenta);

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