<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analíticas de Temperaturas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel de Administrador</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Temperaturas</a>
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
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Temperatura Promedio por Aula (°C)
                    </div>
                    <div class="card-body">
                        <canvas id="aulaBarChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Registros por Campus
                    </div>
                    <div class="card-body" style="max-height: 400px; position: relative;">
                        <canvas id="campusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Función para generar colores aleatorios para los gráficos
        function getRandomColor() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 0.6)`;
        }

        // --- INICIALIZACIÓN DE GRÁFICOS ---
        $(document).ready(function() {
            
            // --- Gráfico de Pastel (Campus) ---
            const pieCtx = document.getElementById('campusPieChart').getContext('2d');
          //   const campusLabels = @json($campusLabels);
          //   const campusData = @json($campusData);
          const campusLabels = {!! json_encode($campusLabels) !!};
            const campusData = {!! json_encode($campusData) !!};

            new Chart(pieCtx, {
                type: 'pie', // Tipo de gráfico
                data: {
                    labels: campusLabels,
                    datasets: [{
                        label: 'Registros',
                        data: campusData,
                        // Genera un color para cada sección del pastel
                        backgroundColor: campusLabels.map(() => getRandomColor()),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // --- Gráfico de Barras (Aulas) ---
            const barCtx = document.getElementById('aulaBarChart').getContext('2d');
          //   const aulaLabels = @json($aulaLabels);
          //   const aulaData = @json($aulaData);
const aulaLabels = {!! json_encode($aulaLabels) !!};
            const aulaData = {!! json_encode($aulaData) !!};

            new Chart(barCtx, {
                type: 'bar', // Tipo de gráfico
                data: {
                    labels: aulaLabels,
                    datasets: [{
                        label: 'Temperatura Promedio (°C)',
                        data: aulaData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Añade '°C' al eje Y
                                callback: function(value) {
                                    return value + ' °C'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>