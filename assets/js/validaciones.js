document.addEventListener('DOMContentLoaded', function() {

    function validarNoVacio(input) {
        return input.value.trim() !== '';
    }

    function soloLetras(valor) {
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor);
    }

    function soloNumeros(valor) {
        return /^[0-9]+$/.test(valor);
    }

    function soloDecimal(valor) {
        return /^[0-9]+(\.[0-9]{1,2})?$/.test(valor);
    }

    function validarDocumento(valor) {
        return /^[VEJ]$/i.test(valor);
    }

    function mostrarError(input, mensaje) {
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains('error-campo')) {
            error = document.createElement('span');
            error.classList.add('error-campo');
            input.parentNode.insertBefore(error, input.nextSibling);
        }
        error.textContent = mensaje;
        input.classList.add('input-error');
    }

    function limpiarError(input) {
        let error = input.nextElementSibling;
        if (error && error.classList.contains('error-campo')) {
            error.textContent = '';
        }
        input.classList.remove('input-error');
    }

    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let valido = true;

            form.querySelectorAll('input, select, textarea').forEach(function(input) {
                limpiarError(input);

                if (input.hasAttribute('required') || input.value.trim() !== '') {

if ((input.name === 'nombre' || input.name === 'apellido' || input.name === 'nombre_categoria' || input.name === 'usuario') && input.value.trim() !== '') {
    if (!validarNoVacio(input)) {
        mostrarError(input, 'No puede estar vacío o solo espacios');
        valido = false;
    } else if (!soloLetras(input.value.trim())) {
        mostrarError(input, 'Solo se permiten letras');
        valido = false;
    }
}

        if ((input.name === 'nombre_producto' || input.name === 'direccion') && input.value.trim() !== '') {
            if (!validarNoVacio(input)) {
                mostrarError(input, 'No puede estar vacío o solo espacios');
                valido = false;
            }
        }

                    if (input.name === 'documento') {
                        if (!validarDocumento(input.value.trim())) {
                            mostrarError(input, 'Debe ser V, E o J');
                            valido = false;
                        }
                    }

                    if (input.name === 'numerodocumento' || input.name === 'buscar_cedula') {
                        if (!soloNumeros(input.value.trim())) {
                            mostrarError(input, 'Solo se permiten números');
                            valido = false;
                        }
                    }

                    if (input.name === 'telefono') {
                        if (!soloNumeros(input.value.trim())) {
                            mostrarError(input, 'Solo se permiten números');
                            valido = false;
                        }
                    }

                    if (input.name === 'precio') {
                        if (!soloDecimal(input.value.trim()) || parseFloat(input.value) <= 0) {
                            mostrarError(input, 'Ingrese un precio válido');
                            valido = false;
                        }
                    }

                    if (input.name === 'iva') {
                        if (!soloDecimal(input.value.trim()) || parseFloat(input.value) < 0 || parseFloat(input.value) > 100) {
                            mostrarError(input, 'Ingrese un IVA válido (0-100)');
                            valido = false;
                        }
                    }

                    if (input.name === 'stock') {
                        if (!soloNumeros(input.value.trim()) || parseInt(input.value) < 0) {
                            mostrarError(input, 'Ingrese un stock válido');
                            valido = false;
                        }
                    }

                    if (input.name && input.name.startsWith('cantidad[')) {
                        let val = parseInt(input.value);
                        let max = parseInt(input.getAttribute('max'));
                        if (isNaN(val) || val < 0) {
                            mostrarError(input, 'Cantidad no válida');
                            valido = false;
                        } else if (val > max) {
                            mostrarError(input, 'No hay suficiente stock');
                            valido = false;
                        }
                    }

                    if (input.type === 'text' && input.value.trim() === '' && input.hasAttribute('required')) {
                        mostrarError(input, 'Este campo es obligatorio');
                        valido = false;
                    }

                }
            });

            if (!valido) {
                e.preventDefault();
            }
        });

        form.querySelectorAll('input, select, textarea').forEach(function(input) {
            input.addEventListener('input', function() {
                limpiarError(input);
            });
        });
    });

    document.querySelectorAll('a[onclick*="confirm"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm(link.getAttribute('onclick').replace("return confirm('", "").replace("')", "").replace('return confirm("', '').replace('")', ''))) {
                e.preventDefault();
            }
        });
    });

});