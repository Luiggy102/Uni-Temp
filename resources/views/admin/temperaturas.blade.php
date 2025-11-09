<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Temperaturas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

        <link rel="icon" href="{{ asset('images/favicon.webp') }}">
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
            <a class="navbar-brand" href="#">Panel de Administrador Ecotec IOT</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">Temperaturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('admin.analiticas') }}">Analíticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('aulas.index') }}">Gestión de Aulas</a>
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
                        <label for="filtro-campus" class="form-label">Campus:</label>
                        <select class="form-select" id="filtro-campus">
                            <option value="">-- Todos los Campus --</option>
                            {{-- Se llenará con JS --}}
                        </select>
                    </div>

                    <div class="col-md-3" id="filtro-edificio-wrapper" style="display: none;">
                        <label for="filtro-edificio" class="form-label">Edificio:</label>
                        <select class="form-select" id="filtro-edificio">
                            <option value="">-- Todos los Edificios --</option>
                            {{-- Se llenará con JS --}}
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filtro-aula" class="form-label">Aula:</label>
                        <select class="form-select" id="filtro-aula">
                            <option value="">-- Todas las Aulas --</option>
                            {{-- Se llenará con JS --}}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="filtro-fecha-desde" class="form-label">Desde:</label>
                        <input type="date" class="form-control" id="filtro-fecha-desde">
                    </div>

                    <div class="col-md-2">
                        <label for="filtro-fecha-hasta" class="form-label">Hasta:</label>
                        <input type="date" class="form-control" id="filtro-fecha-hasta">
                    </div>

                    <div class="col-md-2 d-flex">
                        <button class="btn btn-primary me-2" id="btn-filtrar-fecha">Filtrar</button>
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
const allAulas = {!! json_encode($aulas ?? []) !!};

        $(document).ready(function() {
            
            // --- INICIALIZACIÓN DE DATATABLES ---
            var table = $('#temperaturas-table').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Exportar a PDF',
                        className: 'btn btn-danger',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
                }
            });

            // --- REFERENCIAS A LOS FILTROS ---
            const $campusFilter = $('#filtro-campus');
            const $edificioWrapper = $('#filtro-edificio-wrapper');
            const $edificioFilter = $('#filtro-edificio');
            const $aulaFilter = $('#filtro-aula');
            
            const FECHA_COL = 1, CAMPUS_COL = 2, EDIFICIO_COL = 3, AULA_COL = 4;
            
            const campusMap = {
                'sambo': 'Samborondón',
                'costa': 'Costa',
                'guayaquil': 'Guayaquil'
            };

            // --- PASO 1: Llenar el dropdown de Campus ---
            const campusUnicos = [...new Set(allAulas.map(aula => aula.campus))];
            campusUnicos.sort().forEach(function(campusValue) {
                const campusTexto = campusMap[campusValue] || campusValue;
                $campusFilter.append(new Option(campusTexto, campusValue));
            });

            // --- PASO 2: Lógica cuando cambia el Campus ---
            $campusFilter.on('change', function() {
                const selectedCampus = $(this).val();
                table.column(CAMPUS_COL).search(selectedCampus).draw();

                $edificioFilter.empty().append(new Option('-- Todos los Edificios --', ''));
                $aulaFilter.empty().append(new Option('-- Todas las Aulas --', ''));
                table.column(EDIFICIO_COL).search('').draw();
                table.column(AULA_COL).search('').draw();

                if (selectedCampus === 'sambo') {
                    const edificiosUnicos = [...new Set(
                        allAulas
                            .filter(a => a.campus === 'sambo' && a.edificio !== 'N/A')
                            .map(a => a.edificio)
                    )];
                    edificiosUnicos.sort().forEach(function(edificio) {
                        $edificioFilter.append(new Option(edificio, edificio));
                    });
                    $edificioWrapper.show();
                } else if (selectedCampus) {
                    $edificioWrapper.hide();
                    // Llenar Aulas directamente
                    const aulasDeCampus = allAulas.filter(a => a.campus === selectedCampus);
                    
                    // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                    aulasDeCampus.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));
                    
                    aulasDeCampus.forEach(function(aula) {
                        // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                        $aulaFilter.append(new Option(aula.nombreAula, aula.nombreAula));
                    });
                } else {
                    $edificioWrapper.hide();
                    // Llenar todas las aulas
                    // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                    allAulas.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));
                    allAulas.forEach(function(aula) {
                        // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                        $aulaFilter.append(new Option(aula.nombreAula, aula.nombreAula));
                    });
                }
            });

            // --- PASO 3: Lógica cuando cambia el Edificio ---
            $edificioFilter.on('change', function() {
                const selectedCampus = $campusFilter.val();
                const selectedEdificio = $(this).val();

                table.column(EDIFICIO_COL).search(selectedEdificio).draw();
                $aulaFilter.empty().append(new Option('-- Todas las Aulas --', ''));
                table.column(AULA_COL).search('').draw();

                if (selectedEdificio) {
                    const aulasDeEdificio = allAulas.filter(a => 
                        a.campus === selectedCampus && a.edificio === selectedEdificio
                    );
                    // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                    aulasDeEdificio.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));
                    aulasDeEdificio.forEach(function(aula) {
                        // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                        $aulaFilter.append(new Option(aula.nombreAula, aula.nombreAula));
                    });
                }
            });

            // --- PASO 4: Lógica cuando cambia el Aula ---
            $aulaFilter.on('change', function() {
                // El valor aquí es el 'nombreAula', pero la columna de la tabla
                // de temperaturas (AULA_COL = 4) tiene el 'aula', ¡que debería ser lo mismo!
                // Si esto no funciona, revisa tu DashboardController@getAllTemperaturas
                // para asegurarte que $temp['aula'] contiene el nombre del aula.
                table.column(AULA_COL).search($(this).val()).draw();
            });

            // --- LÓGICA DE FILTROS DE FECHA ---
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // ... (esta lógica de fecha se queda igual) ...
                    var min = $('#filtro-fecha-desde').val();
                    var max = $('#filtro-fecha-hasta').val();
                    var fecha = data[FECHA_COL] || '';
                    var fechaSolo = fecha.split(' ')[0];
                    if ( (min === '' || min === null) && (max === '' || max === null) ||
                         (min === '' || min === null) && fechaSolo <= max ||
                         (min <= fechaSolo) && (max === '' || max === null) ||
                         (min <= fechaSolo) && (fechaSolo <= max) ) {
                        return true;
                    }
                    return false;
                }
            );

            $('#btn-filtrar-fecha').on('click', function() {
                table.draw();
            });

            $('#btn-limpiar').on('click', function() {
                $('#filtro-fecha-desde').val('');
                $('#filtro-fecha-hasta').val('');
                $campusFilter.val('');
                $edificioFilter.val('').empty().append(new Option('-- Todos los Edificios --', ''));
                $aulaFilter.val('').empty().append(new Option('-- Todas las Aulas --', ''));
                $edificioWrapper.hide();
                table.search('').columns().search('').draw();

                // Recarga todas las aulas al limpiar
                // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                allAulas.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));
                allAulas.forEach(function(aula) {
                    // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                    $aulaFilter.append(new Option(aula.nombreAula, aula.nombreAula));
                });
            });

            // Carga inicial de aulas (Todas)
            // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
            allAulas.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));
            allAulas.forEach(function(aula) {
                // <-- ¡CAMBIO AQUÍ! (de .nombre a .nombreAula)
                $aulaFilter.append(new Option(aula.nombreAula, aula.nombreAula));
            });

        });

    </script>
    
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>