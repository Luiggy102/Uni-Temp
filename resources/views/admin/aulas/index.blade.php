<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aulas</title>

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
        <link rel="icon" href="{{ asset('images/favicon.webp') }}">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel de Administrador Ecotec IOT</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Temperaturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.analiticas') }}">Analíticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('aulas.index') }}">Gestión de Aulas</a>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Gestión de Aulas</h3>
            {{-- Botón para abrir el Modal de Creación (controlado por JS) --}}
            <button type="button" class="btn btn-primary" id="btn-crear-aula">
                Crear Nueva Aula
            </button>
        </div>

        {{-- Mostrar mensajes de éxito o error --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card filters mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filtro-campus" class="form-label">Filtrar por Campus:</label>
                        <select class="form-select" id="filtro-campus">
                            <option value="">-- Todos los Campus --</option>
                            <option value="sambo">Samborondón</option>
                            <option value="costa">Costa</option>
                            <option value="guayaquil">Guayaquil</option>
                        </select>
                    </div>
<div class="col-md-4" id="filtro-edificio-wrapper">
                    <label for="filtro-edificio" class="form-label">Filtrar por Edificio:</label>
                    <select class="form-select" id="filtro-edificio">
                        <option value="">-- Todos los Edificios --</option>
                        <option value="N/A">N/A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="ANEXO">ANEXO</option>
                    </select>
                </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-secondary" id="btn-limpiar-filtros">Limpiar Filtros</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                {{-- 
                    Atributos data-* para pasar rutas de Laravel al archivo .js externo.
                    Esto mantiene el JS "limpio" de sintaxis Blade.
                --}}
                <table id="aulas-table" class="table table-striped table-bordered" style="width:100%"
                       data-url-store="{{ route('aulas.store') }}"
                       data-url-edit-base="{{ route('aulas.index') }}" 
                       data-datatable-lang-url="https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Campus</th>
                            <th>Edificio</th>
                            <th>Nombre del Aula</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aulas as $aula)
                            <tr>
                                <td>{{ $aula['id'] }}</td>
                                <td>{{ $aula['campus'] ?? 'N/A' }}</td>
                                <td>{{ $aula['edificio'] ?? 'N/A' }}</td>
                                <td>{{ $aula['nombreAula'] ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $aula['id'] }}">Editar</button>
                                    
                                    <form action="{{ route('aulas.destroy', $aula['id']) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta aula?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aulaModal" tabindex="-1" aria-labelledby="aulaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aulaModalLabel">Crear Nueva Aula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{-- Este formulario será manipulado por aulas.js --}}
                <form id="aula-form" method="POST">
                    @csrf
                    <div id="form-method-spoof"></div>
                    
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="form-campus" class="form-label">Campus (*)</label>
                            <select class="form-select" id="form-campus" name="campus" required>
                                <option value="" disabled selected>Seleccione un campus...</option>
                                <option value="sambo">Samborondón</option>
                                <option value="costa">Costa</option>
                                <option value="guayaquil">Guayaquil</option>
                            </select>
                        </div>

                        <div class="mb-3" id="edificio-wrapper" style="display: none;">
                            <label for="form-edificio" class="form-label">Edificio (*)</label>
                            <select class="form-select" id="form-edificio" name="edificio">
                                <option value="">Seleccione un edificio...</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="ANEXO">ANEXO</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="form-nombre" class="form-label">Nombre del Aula (*)</label>
                            <input type="text" class="form-control" id="form-nombre" name="nombreAula" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
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

    <script src="{{ asset('js/admin/aulas.js') }}"></script>
</body>
</html>