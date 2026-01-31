<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro Temperatura</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
        </style>
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="{{ asset('images/favicon.webp') }}">
</head>

<body class="bg-light py-4">

    <div class="container p-5">
        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white p-4 d-flex flex-column flex-md-row align-items-center">

                <img src="{{ asset('images/logo.png') }}" alt="Logo Banner de la Universidad"
                    class="img-fluid mb-3 mb-md-0 me-md-4" style="max-width: 100px;">

                <div class="text-center text-md-start">
                    <h4 class="mb-0">Reporte de Temperaturas de Aulas en Campuses de la Universidad Ecotec IOT</h4>
                    <div class="mt-2" style="font-size: 0.9em;">
                        <span>
                            <strong>Sesión de Usuario:</strong> {{ request()->ip() }}
                        </span>
                        <span class="mx-2">|</span>
                        <span>
                            <strong>Servidor Host:</strong> {{ gethostname() }}
                        </span>
                    </div>
                </div>

            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('warning') }}
                    </div>
                @endif



                <form action="{{ route('temperaturas.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="ip_sesion" value="{{ request()->ip() }}">

                    <div class="mb-3">
                        <label for="campus" class="form-label">Campus</label>
                        <select class="form-select" id="campus" name="campus" required>
                            <option value="">Seleccione un campus</option>
                        </select>
                    </div>

                    <div class="mb-3" id="edificio-wrapper" style="display: none;">
                        <label for="edificio" class="form-label">Edificio</label>
                        <select class="form-select" id="edificio" name="edificio" required>
                            <option value="">Seleccione un edificio</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="aula" class="form-label">Aula</label>
                        <select class="form-select" id="aula" name="aula_nombre" required disabled>
                            <option value="">Seleccione... (primero elija campus)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="temperatura" class="form-label">Temperatura (°C)</label>
                        <input type="number" step="0.1" min="2" max="50" class="form-control" id="temperatura"
                            name="temperatura" required placeholder="Ejemplo: 28.5">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-4">Registrar Temperatura</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

<script>
    // 3. Pasa todos los datos de aulas de Laravel a JavaScript
    // (Asumiendo que $aulas es un array/colección de objetos)
    const allAulas = {!! json_encode($aulas ?? []) !!};

    $(document).ready(function () {

        // 4. Referencias a los dropdowns
        const $campusSelect = $('#campus');
        const $edificioWrapper = $('#edificio-wrapper');
        const $edificioSelect = $('#edificio');
        const $aulaSelect = $('#aula');

        // --- PASO 1: Llenar el dropdown de Campus (con valores únicos) ---
        // 'sambo', 'costa', 'guayaquil', etc.
        const campusMap = {
            'sambo': 'Samborondón',
            'costa': 'Costa',
            'guayaquil': 'Guayaquil'
        };

        const campusUnicos = [...new Set(allAulas.map(aula => aula.campus))];


        campusUnicos.sort().forEach(function (campusValue) {
            // 3. Busca el nombre amigable en el mapa.
            //    Si no lo encuentra, usa el valor original capitalizado.
            const campusTexto = campusMap[campusValue] ||
                (campusValue.charAt(0).toUpperCase() + campusValue.slice(1));

            // 4. Añade la opción:
            //    value="sambo", texto="Samborondón"
            $campusSelect.append(new Option(campusTexto, campusValue));
        });

        // --- PASO 2: Lógica cuando cambia el Campus ---
        $campusSelect.on('change', function () {
            const selectedCampus = $(this).val();

            // Resetea los dropdowns dependientes
            $edificioSelect.empty().append(new Option('Seleccione un edificio', ''));
            $aulaSelect.empty().append(new Option('Seleccione...', '')).prop('disabled', true);

            // Regla de negocio: Solo 'sambo' tiene edificios
            if (selectedCampus === 'sambo') {

                // Llenar Edificios (B, C, D, ANEXO)
                const edificiosUnicos = [...new Set(
                    allAulas
                        .filter(aula => aula.campus === 'sambo' && aula.edificio !== 'N/A')
                        .map(aula => aula.edificio)
                )];

                edificiosUnicos.sort().forEach(function (edificio) {
                    $edificioSelect.append(new Option(edificio, edificio));
                });

                // Mostrar y hacer requerido el campo
                $edificioWrapper.show();
                $edificioSelect.prop('required', true);

            } else if (selectedCampus) {
                // Si es 'costa' o 'guayaquil' (o cualquier otro)

                // Ocultar y no hacer requerido el campo
                $edificioWrapper.hide();
                $edificioSelect.prop('required', false);

                // Llenar Aulas directamente
                const aulasDeCampus = allAulas.filter(aula => aula.campus === selectedCampus);

                aulasDeCampus.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula)); // Ordena alfabéticamente

                aulasDeCampus.forEach(function (aula) {
                    $aulaSelect.append(new Option(aula.nombreAula, aula.nombreAula));
                });

                $aulaSelect.prop('disabled', false); // Habilita el dropdown
            } else {
                // Si des-seleccionan el campus
                $edificioWrapper.hide();
                $edificioSelect.prop('required', false);
            }
        });

        // --- PASO 3: Lógica cuando cambia el Edificio ---
        $edificioSelect.on('change', function () {
            const selectedCampus = $campusSelect.val();
            const selectedEdificio = $(this).val();

            $aulaSelect.empty().append(new Option('Seleccione un aula', '')).prop('disabled', true);

            if (selectedEdificio) {
                // Llenar Aulas (solo de ese campus y edificio)
                const aulasDeEdificio = allAulas.filter(aula =>
                    aula.campus === selectedCampus && aula.edificio === selectedEdificio
                );

                aulasDeEdificio.sort((a, b) => a.nombreAula.localeCompare(b.nombreAula));

                aulasDeEdificio.forEach(function (aula) {
                    $aulaSelect.append(new Option(aula.nombreAula, aula.nombreAula));
                });

                $aulaSelect.prop('disabled', false);
            }
        });
    });
</script>

</html>