<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Temperaturas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* Estilos para los filtros */
        .filters {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: .5rem;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel de Administrador</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">Temperaturas</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.analiticas') }}">Analíticas</a>
                    </li>
               </ul>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid mt-4">

        <div class="card filters mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filtro-aula" class="form-label">Filtrar por Aula:</label>
                        <select class="form-select" id="filtro-aula">
                            <option value="">-- Todas las Aulas --</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula['nombre'] }}">{{ $aula['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filtro-fecha-desde" class="form-label">Desde:</label>
                        <input type="date" class="form-control" id="filtro-fecha-desde">
                    </div>

                    <div class="col-md-3">
                        <label for="filtro-fecha-hasta" class="form-label">Hasta:</label>
                        <input type="date" class="form-control" id="filtro-fecha-hasta">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary" id="btn-filtrar">Filtrar</button>
                        <button class="btn btn-secondary" id="btn-limpiar">Limpiar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="temperaturas-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Campus</th>
                            <th>Edificio</th>
                            <th>Aula</th>
                            <th>Temperatura (°C)</th>
                            <th>IP Sesión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($temperaturas as $temp)
                            <tr>
                                <td>{{ $temp['id'] }}</td>
                                <td>{{ $temp['fecha'] }}</td>
                                <td>{{ $temp['campus'] }}</td>
                                <td>{{ $temp['edificio'] }}</td>
                                <td>{{ $temp['aula'] }}</td>
                                <td>{{ $temp['temperatura'] }}</td>
                                <td>{{ $temp['ip_sesion'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>


    <script>
        $(document).ready(function() {
            
            // --- INICIALIZACIÓN DE DATATABLES ---
            var table = $('#temperaturas-table').DataTable({
                // Habilita los botones
                dom: 'lBfrtip', // 'l' - length, 'B' - buttons, 'f' - filter, 'r' - processing, 't' - table, 'i' - info, 'p' - pagination
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-file-earmark-pdf-fill"></i> Exportar a PDF',
                        className: 'btn btn-danger',
                         orientation: 'landscape', // 1. Pone la hoja horizontal
                        pageSize: 'LEGAL' // 2. (Opcional) Usa un papel más ancho
                    }
                ],
                // Configura el idioma a español
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
                }
            });

            // --- LÓGICA DE FILTROS PERSONALIZADOS ---

            // Índice de la columna 'Aula' (empezando en 0)
            const AULA_COL_INDEX = 4;
            // Índice de la columna 'Fecha'
            const FECHA_COL_INDEX = 1;

            // Filtro para el Dropdown de Aula
            $('#filtro-aula').on('change', function() {
                var valor = $(this).val();
                // Busca en la columna de Aula (índice 4), con búsqueda exacta
               //  table.column(AULA_COL_INDEX).search(valor ? '^' + valor + '$' : '', true, false).draw();
               table.column(AULA_COL_INDEX).search(valor).draw();
            });

            // Filtro para rango de fechas
            // Necesita una función de búsqueda personalizada
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $('#filtro-fecha-desde').val();
                    var max = $('#filtro-fecha-hasta').val();
                    var fecha = data[FECHA_COL_INDEX] || ''; // Obtiene la fecha de la tabla (índice 1)

                    // Extrae solo la parte de la fecha (YYYY-MM-DD)
                    var fechaSolo = fecha.split(' ')[0]; 

                    if (
                        (min === '' || min === null) && (max === '' || max === null) ||
                        (min === '' || min === null) && fechaSolo <= max ||
                        (min <= fechaSolo) && (max === '' || max === null) ||
                        (min <= fechaSolo) && (fechaSolo <= max)
                    ) {
                        return true; // Mostrar fila
                    }
                    return false; // Ocultar fila
                }
            );

            // Botón para aplicar el filtro de fechas
            $('#btn-filtrar').on('click', function() {
                table.draw(); // Vuelve a dibujar la tabla, aplicando el filtro de fechas
            });

            // Botón para limpiar todos los filtros
            $('#btn-limpiar').on('click', function() {
                // Limpia los inputs
                $('#filtro-aula').val('');
                $('#filtro-fecha-desde').val('');
                $('#filtro-fecha-hasta').val('');
                
                // Limpia el filtro de DataTables
                table.column(AULA_COL_INDEX).search('').draw();
                
                // Vuelve a dibujar (aplicando el filtro de fechas limpias)
                table.draw();
            });

        });
    </script>
    
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>