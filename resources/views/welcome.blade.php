<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Registro Temperatura</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
            </style>
        @endif
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="icon" href="{{ asset('images/favicon.webp') }}">
    </head>
    
<body class="bg-light py-4">

<div class="container p-5">
    <div class="card shadow-sm">

<div class="card-header bg-primary text-white p-4 d-flex flex-column flex-md-row align-items-center">

            <img src="{{ asset('images/logo.png') }}" 
                 alt="Logo Banner de la Universidad" 
                 class="img-fluid mb-3 mb-md-0 me-md-4" 
                 style="max-width: 100px;">

            <div class="text-center text-md-start">
                <h4 class="mb-0">Reporte de Temperaturas de Aulas en Campuses de la Universidad Ecotec</h4>
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



            <form action="{{ route('temperaturas.store') }}" method="POST">
                @csrf

                <input type="hidden" name="ip_sesion" value="{{ request()->ip() }}">
                
                <div class="mb-3">
                    <label for="campus" class="form-label">Campus</label>
                    <select class="form-select" id="campus" name="campus" required>
                        <option value="">Seleccione un campus</option>
                        <option value="Samborondon">Samborondón</option>
                        <option value="Guayaquil" disabled>Guayaquil</option>
                        <option value="Costa" disabled>Costa</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="edificio" class="form-label">Edificio</label>
                    <select class="form-select" id="edificio" name="edificio" required>
                        <option value="">Seleccione un edificio</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="Anexo">Anexo</option>
                    </select>
                </div>

<div class="mb-3">
    <label for="aula" class="form-label">Aula</label>
    {{-- 1. Cambiamos el 'name' a 'aula_nombre' --}}
    <select class="form-select" id="aula" name="aula_nombre" required>
        <option value="">Seleccione un aula</option>
        @forelse($aulas as $aula)
            {{-- 2. Cambiamos el 'value' para que envíe el nombre --}}
            <option value="{{ $aula->nombre }}">{{ $aula->nombre }}</option>
        @empty
            <option value="" disabled>No hay aulas disponibles</option>
        @endforelse
    </select>
</div>

                <div class="mb-3">
                    <label for="temperatura" class="form-label">Temperatura (°C)</label>
                    <input type="number" step="0.1" class="form-control" id="temperatura" name="temperatura" required placeholder="Ejemplo: 28.5">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Registrar Temperatura</button>
                </div>
            </form>





        </div>
    </div>
</div>
</body>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</html>
