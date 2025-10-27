<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

class UniversidadController extends Controller
{
    protected $dynamoDb;
    public function __construct(DynamoDbClient $dynamoDb)
    {
        $this->dynamoDb = $dynamoDb;
    }
    public function showForm()
    {
        $aulas = $this->getAulas();

        return view('welcome', [
            'aulas' => $aulas
        ]);
    }
    private function getAulas(): array
    {
        $tableName = 'aulas_ecotec_iot';
        $formattedAulas = [];

        try {
            //  'scan' para obtener todos los items
            $result = $this->dynamoDb->scan([
                'TableName' => $tableName
            ]);

            // Formatea los resultados
            foreach ($result['Items'] as $item) {
                // Crea un objeto estándar para que el Blade ($aula->nombre) funcione
                $aulaObj = new \stdClass();

                // Mapea los campos de DynamoDB a propiedades simples
                $aulaObj->id = $item['id']['N'] ?? null;
                $aulaObj->campus = $item['campus']['S'] ?? null;

                // Mapeo clave: 
                // Lee 'nombreAula' de DynamoDB y lo asigna a 'nombre' para el Blade
                $aulaObj->nombre = $item['nombreAula']['S'] ?? 'Nombre no disponible';

                $formattedAulas[] = $aulaObj;
            }

        } catch (DynamoDbException $e) {
            report($e);
            return [];
        }

        return $formattedAulas;
    }
}
