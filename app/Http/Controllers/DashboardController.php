<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $dynamoDb;
    protected $marshaler;

    public function __construct(DynamoDbClient $dynamoDb)
    {
        $this->dynamoDb = $dynamoDb;
        $this->marshaler = new Marshaler();
    }

    /**
     * Muestra la página principal del dashboard de temperaturas.
     */
    public function showTemperaturas()
    {
        // 1. Obtenemos las aulas para el filtro dropdown
        $aulas = $this->getAulas();

        // 2. Obtenemos TODOS los registros de temperatura
        // (¡Advertencia! Esto puede ser lento si hay millones de registros)
        $temperaturas = $this->getAllTemperaturas();

        // 3. Pasamos los datos a la vista
        return view('admin.temperaturas', [
            'aulas' => $aulas,
            'temperaturas' => $temperaturas
        ]);
    }

    /**
     * Obtiene la lista única de aulas de la tabla de aulas.
     */
    private function getAulas(): array
    {
        $tableName = 'aulas_ecotec_iot';
        $aulas = [];
        $params = ['TableName' => $tableName];

        try {
            do {
                $result = $this->dynamoDb->scan($params);

                foreach ($result['Items'] as $item) {
                    $aulas[] = $this->unmarshalItem($item);
                }

                $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'] ?? null;
            } while ($params['ExclusiveStartKey']);

        } catch (\Exception $e) {
            report($e); // Reporta el error
            return []; // Devuelve vacío en caso de error
        }

        return $aulas;

    }

    /**
     * Escanea y obtiene TODOS los registros de la tabla de temperaturas.
     * Utiliza paginación de 'scan' para manejar grandes volúmenes.
     */
    private function getAllTemperaturas(): array
    {
        $tableName = 'registro_temperaturas_iot';
        $formattedItems = [];
        $params = ['TableName' => $tableName];

        try {
            do {
                $result = $this->dynamoDb->scan($params);

                foreach ($result['Items'] as $item) {
                    // Convierte el formato de DynamoDB a un array simple
                    $formattedItems[] = $this->unmarshalItem($item);
                }

                // Prepara la "llave" para la siguiente página de resultados
                $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'] ?? null;

            } while ($params['ExclusiveStartKey']); // Repite mientras haya más páginas

        } catch (\Exception $e) {
            report($e);
            return []; // Devuelve vacío en caso de error
        }

        return $formattedItems;
    }

    /**
     * Helper para convertir un item de DynamoDB (con 'S', 'N') a un array PHP.
     */
    private function unmarshalItem($item): array
    {
        $obj = new \stdClass();
        foreach ($item as $key => $value) {
            $obj->$key = $this->marshaler->unmarshalValue($value);
        }
        return (array) $obj;
    }
    /**
     * Muestra la página de analíticas con gráficos.
     */
    public function showAnaliticas()
    {
        // 1. Obtiene todos los datos y los convierte a una Colección de Laravel
        $temperaturas = collect($this->getAllTemperaturas())
            ->map(function ($item) {
                // Asegura que los campos clave existan y tengan el tipo correcto
                if (empty($item['fecha']) || empty($item['temperatura'])) {
                    return null; // Descarta registros malos
                }
                $item['fecha'] = Carbon::parse($item['fecha']);
                $item['temperatura'] = (float) $item['temperatura'];
                return $item;
            })
            ->filter(); // Elimina cualquier registro nulo

        // --- 2. CÁLCULO DE MÉTRICAS KPI ---
        $now = Carbon::now();
        $recs24h = $temperaturas->where('fecha', '>=', $now->copy()->subDay());

        $kpis = [
            'maxTemp24h' => $recs24h->isNotEmpty() ? $recs24h->max('temperatura') : null,
            'avgTempAll' => $temperaturas->isNotEmpty() ? round($temperaturas->avg('temperatura'), 1) : null,
            'alertCount24h' => $recs24h->where('temperatura', '>', 28)->count(), // Alertas > 28°C
            'totalRecords' => $temperaturas->count(),
        ];

        // --- 3. GRÁFICO DE LÍNEA: TEMP POR HORA ---
        $tempPorHora = $temperaturas
            ->groupBy(fn($item) => $item['fecha']->format('H')) // Agrupa por hora (00, 01, ...)
            ->map(fn($group) => round($group->avg('temperatura'), 1))
            ->sortKeys(); // Ordena por hora

        // Rellena las horas faltantes con 'null' para un gráfico limpio
        $tempPorHoraData = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $tempPorHoraData[$hour . ':00'] = $tempPorHora->get($hour, null);
        }
        $tempPorHoraFinal = [
            'labels' => array_keys($tempPorHoraData),
            'data' => array_values($tempPorHoraData),
        ];

        // --- 4. GRÁFICO DE DONA: REGISTROS POR CAMPUS ---
        $campusDist = $temperaturas
            ->groupBy('campus')
            ->map(fn($group) => $group->count());
        $campusDistFinal = [
            'labels' => $campusDist->keys(),
            'data' => $campusDist->values(),
        ];

        // --- 5. GRÁFICOS DE BARRAS: TOP AULAS ---
        $avgPorAula = $temperaturas
            ->groupBy('aula') // Asegúrate que tu tabla 'temperaturas' tenga el campo 'aula'
            ->map(fn($group) => round($group->avg('temperatura'), 1));

        // Top 5 Calientes
        $topCalientes = $avgPorAula->sortDesc()->take(5);
        $topCalientesFinal = [
            'labels' => $topCalientes->keys(),
            'data' => $topCalientes->values(),
        ];

        // Top 5 Frías
        $topFrios = $avgPorAula->sort()->take(5);
        $topFriosFinal = [
            'labels' => $topFrios->keys(),
            'data' => $topFrios->values(),
        ];

        // 6. Pasar los datos YA CODIFICADOS COMO JSON a la vista
        return view('admin.analiticas', [
            'kpis' => json_encode($kpis),
            'tempPorHora' => json_encode($tempPorHoraFinal),
            'campusDist' => json_encode($campusDistFinal),
            'topCalientes' => json_encode($topCalientesFinal),
            'topFrios' => json_encode($topFriosFinal),
        ]);
    }

}