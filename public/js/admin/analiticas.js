$(document).ready(function () {

     // --- 1. PALETA DE COLORES Y LECTURA DE DATOS ---

     // Define una paleta de colores fija para que los gráficos sean consistentes
     const CHART_COLORS = [
          'rgba(54, 162, 235, 0.6)', // Azul
          'rgba(255, 99, 132, 0.6)', // Rojo
          'rgba(255, 206, 86, 0.6)', // Amarillo
          'rgba(75, 192, 192, 0.6)', // Verde
          'rgba(153, 102, 255, 0.6)',// Morado
          'rgba(255, 159, 64, 0.6)' // Naranja
     ];
     const CHART_BORDERS = [
          'rgba(54, 162, 235, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
     ];

     // Lee los datos JSON del div #analytics-data
     // jQuery.data() automáticamente parsea el JSON por nosotros
     const $dataElement = $('#analytics-data');
     const kpis = $dataElement.data('kpis');
     const tempPorHora = $dataElement.data('temp-por-hora');
     const campusDist = $dataElement.data('campus-dist');
     const topCalientes = $dataElement.data('top-calientes');
     const topFrios = $dataElement.data('top-frios');

     // --- 2. LLENAR LAS TARJETAS KPI ---
     if (kpis) {
          $('#kpi-max-temp').text(kpis.maxTemp24h ? kpis.maxTemp24h.toFixed(1) + ' °C' : '--');
          $('#kpi-avg-temp').text(kpis.avgTempAll ? kpis.avgTempAll.toFixed(1) + ' °C' : '--');
          $('#kpi-alerts').text(kpis.alertCount24h || '0');
          $('#kpi-total').text(kpis.totalRecords || '0');
     }

     // --- 3. GRÁFICO DE LÍNEA: TEMPERATURA POR HORA ---
     if (tempPorHora && document.getElementById('lineChartTempPorHora')) {
          const ctx = document.getElementById('lineChartTempPorHora').getContext('2d');
          new Chart(ctx, {
               type: 'line',
               data: {
                    labels: tempPorHora.labels, // ["00:00", "01:00", ...]
                    datasets: [{
                         label: 'Temperatura Promedio (°C)',
                         data: tempPorHora.data, // [22.5, 22.4, ...]
                         borderColor: CHART_BORDERS[0],
                         backgroundColor: CHART_COLORS[0],
                         fill: true,
                         tension: 0.1 // Suaviza la línea
                    }]
               },
               options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                         y: {
                              ticks: {
                                   callback: function (value) {
                                        return value + ' °C'; // Añade °C al eje
                                   }
                              }
                         }
                    }
               }
          });
     }

     // --- 4. GRÁFICO DE DONA: DISTRIBUCIÓN POR CAMPUS ---
     if (campusDist && document.getElementById('doughnutChartCampus')) {
          const ctx = document.getElementById('doughnutChartCampus').getContext('2d');
          new Chart(ctx, {
               type: 'doughnut',
               data: {
                    labels: campusDist.labels, // ["sambo", "costa", ...]
                    datasets: [{
                         label: 'Registros',
                         data: campusDist.data, // [120, 55, ...]
                         backgroundColor: CHART_COLORS,
                         borderColor: CHART_BORDERS,
                         borderWidth: 1
                    }]
               },
               options: {
                    responsive: true,
                    maintainAspectRatio: false
               }
          });
     }

     // --- 5. GRÁFICO DE BARRAS H: TOP 5 CALIENTES ---
     if (topCalientes && document.getElementById('barChartTopCalientes')) {
          const ctx = document.getElementById('barChartTopCalientes').getContext('2d');
          new Chart(ctx, {
               type: 'bar',
               data: {
                    labels: topCalientes.labels, // ["Aula D", "Aula B", ...]
                    datasets: [{
                         label: 'Temperatura Promedio (°C)',
                         data: topCalientes.data, // [28.5, 27.9, ...]
                         backgroundColor: CHART_COLORS[1], // Rojo
                         borderColor: CHART_BORDERS[1],
                         borderWidth: 1
                    }]
               },
               options: {
                    indexAxis: 'y', // <-- Esto lo hace horizontal
                    responsive: true,
                    scales: {
                         x: {
                              beginAtZero: true,
                              ticks: {
                                   callback: function (value) {
                                        return value + ' °C';
                                   }
                              }
                         }
                    }
               }
          });
     }

     // --- 6. GRÁFICO DE BARRAS H: TOP 5 FRÍOS ---
     if (topFrios && document.getElementById('barChartTopFrios')) {
          const ctx = document.getElementById('barChartTopFrios').getContext('2d');
          new Chart(ctx, {
               type: 'bar',
               data: {
                    labels: topFrios.labels, // ["Aula C", "Aula A", ...]
                    datasets: [{
                         label: 'Temperatura Promedio (°C)',
                         data: topFrios.data, // [21.5, 22.1, ...]
                         backgroundColor: CHART_COLORS[0], // Azul
                         borderColor: CHART_BORDERS[0],
                         borderWidth: 1
                    }]
               },
               options: {
                    indexAxis: 'y', // <-- Horizontal
                    responsive: true,
                    scales: {
                         x: {
                              beginAtZero: true,
                              ticks: {
                                   callback: function (value) {
                                        return value + ' °C';
                                   }
                              }
                         }
                    }
               }
          });
     }

});