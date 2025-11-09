<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient; // 1. Importa el cliente
use Aws\DynamoDb\Marshaler; // 2. Importa el Marshaler

class UniversidadController extends Controller
{
    protected $dynamoDb;
    protected $marshaler;
    protected $aulasTableName = 'aulas_ecotec_iot';

    // 4. Actualiza el constructor
    public function __construct(DynamoDbClient $dynamoDb)
    {
        $this->dynamoDb = $dynamoDb;
        $this->marshaler = new Marshaler();
    }

    /**
     * Muestra el formulario principal (welcome.blade.php)
     */
    public function showForm()
    {
        // 5. Ahora esto SÍ funcionará
        $aulas = $this->getAllAulas();

        // Asegúrate de que el nombre de la vista ('welcome') sea correcto
        return view('welcome', [
            'aulas' => $aulas
        ]);
    }

    // ==========================================================
    // --- MÉTODOS FALTANTES (COPIADOS DE AULASCONTROLLER) ---
    // ==========================================================

    /**
     * Helper para escanear y obtener todas las aulas.
     */
    private function getAllAulas(): array
    {
        $formattedItems = [];
        $params = ['TableName' => $this->aulasTableName];

        try {
            do {
                $result = $this->dynamoDb->scan($params);

                foreach ($result['Items'] as $item) {
                    $formattedItems[] = $this->unmarshalItem($item);
                }

                $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'] ?? null;
            } while ($params['ExclusiveStartKey']);

        } catch (\Exception $e) {
            report($e);
            return [];
        }

        return $formattedItems;
    }

    /**
     * Helper para convertir un item de DynamoDB a un array PHP.
     */
    private function unmarshalItem($item): array
    {
        $obj = new \stdClass();
        foreach ($item as $key => $value) {
            $obj->$key = $this->marshaler->unmarshalValue($value);
        }
        return (array) $obj;
    }
}