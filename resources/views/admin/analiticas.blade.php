<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analíticas de Temperaturas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="icon" href="{{ asset('images/favicon.webp') }}">
    <style>
        /* Estilos para las tarjetas KPI */
        .kpi-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .kpi-value {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .kpi-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            color: #6c757d;
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Temperaturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.analiticas') }}">Analíticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('aulas.index') }}">Gestión de Aulas</a>
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

        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm kpi-card">
                    <div class="card-body text-center">
                        <div class="kpi-label">Temp. Máx (24h)</div>
                        <div class="kpi-value text-danger" id="kpi-max-temp">--</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm kpi-card">
                    <div class="card-body text-center">
                        <div class="kpi-label">Temp. Promedio (Gral)</div>
                        <div class="kpi-value text-info" id="kpi-avg-temp">--</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm kpi-card">
                    <div class="card-body text-center">
                        <div class="kpi-label">Alertas de Calor (>28°C)</div>
                        <div class="kpi-value text-warning" id="kpi-alerts">--</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm kpi-card">
                    <div class="card-body text-center">
                        <div class="kpi-label">Total Registros</div>
                        <div class="kpi-value text-muted" id="kpi-total">--</div>
                    </div>
                </div>
            </div>
        </div> <div class="row mt-4">
<div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                Temperatura Promedio por Hora del Día
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="lineChartTempPorHora"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                Distribución de Registros por Campus
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="doughnutChartCampus"></canvas>
            </div>
        </div>
    </div>

        </div> <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Top 5 Aulas Más Calientes (Promedio)
                    </div>
                    <div class="card-body">
                        <canvas id="barChartTopCalientes"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Top 5 Aulas Más Frías (Promedio)
                    </div>
                    <div class="card-body">
                        <canvas id="barChartTopFrios"></canvas>
                    </div>
                </div>
            </div>
        </div> </div> <div id="analytics-data"
         data-kpis="{{ $kpis ?? '' }}"
         data-temp-por-hora="{{ $tempPorHora ?? '' }}"
         data-campus-dist="{{ $campusDist ?? '' }}"
         data-top-calientes="{{ $topCalientes ?? '' }}"
         data-top-frios="{{ $topFrios ?? '' }}"
    ></div>

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="{{ asset('js/admin/analiticas.js') }}"></script>

</body>
</html>