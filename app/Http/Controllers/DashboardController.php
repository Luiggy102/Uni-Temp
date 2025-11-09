<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

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
        // 1. Obtener todos los datos y convertirlos a una Colección de Laravel
        $temperaturas = collect($this->getAllTemperaturas());

        // 2. Procesar datos para los gráficos

        // --- Gráfico 1: Registros por Campus (para un Pie Chart) ---
        // Agrupa por 'campus' y cuenta cuántos registros hay en cada grupo
        $dataCampus = $temperaturas->groupBy('campus')
            ->map(fn($group) => $group->count());

        // --- Gráfico 2: Temperatura Promedio por Aula (para un Bar Chart) ---
        // Agrupa por 'aula', saca el promedio de 'temperatura' y redondea a 2 decimales
        $dataAvgAula = $temperaturas->groupBy('aula')
            ->map(fn($group) => round($group->avg('temperatura'), 2))
            ->sortDesc(); // Ordena del más caliente al más frío


        // 3. Pasar los datos ya procesados a la vista
        return view('admin.analiticas', [
            'campusLabels' => $dataCampus->keys(), // -> ['Samborondon', 'Guayaquil', ...]
            'campusData' => $dataCampus->values(), // -> [120, 55, ...]

            'aulaLabels' => $dataAvgAula->keys(),   // -> ['Aula D', 'Aula B', ...]
            'aulaData' => $dataAvgAula->values(), // -> [25.5, 24.1, ...]
        ]);
    }
}