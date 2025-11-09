<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TemperaturaController extends Controller
{
    protected $sqs;
    protected $marshaler;

    public function __construct(SqsClient $sqs)
    {
        $this->sqs = $sqs;
    }

    /**
     * Almacena el nuevo registro de temperatura.
     */
    public function store(Request $request)
    {


        $itemData = [
            'id' => (string) Str::uuid(),
            'fecha' => Carbon::now()->toDateTimeString(),
            'campus' => $request->input('campus'),
            'edificio' => $request->input('edificio'),

            // Leemos el 'aula_nombre' del formulario en lugar de 'aula_id'
            'aula' => $request->input('aula_nombre'),

            'temperatura' => (string) $request->input('temperatura'),
            'ip_sesion' => $request->input('ip_sesion')
        ];

        // El resto del código (marshalJson, putItem, try/catch) 
        // se queda exactamente igual.

        try {

            $this->sqs->sendMessage([
                'QueueUrl' => env('SQS_QUEUE_URL'),
                'MessageBody' => json_encode($itemData)
            ]);

            return redirect()->back()->with('success', 'Temperatura registrada exitosamente.');

        } catch (AwsException $e) {
            report($e);
            return redirect()->back()->with('warning', 'Temperatura registrada, pero falló al encolar el mensaje: ' . $e->getMessage());
        }

    }

    /**
     * Función privada para obtener los detalles de un aula por su ID
     * desde la tabla 'aulas_xxxxxx_iot'.
     */
    private function getAulaInfo($id): array
    {
        // Si no se proporciona ID (ej. 'Seleccione un aula')
        if (!$id) {
            return ['nombre' => 'Aula no especificada', 'campus' => null];
        }

        try {
            // Prepara la clave para la búsqueda
            $key = $this->marshaler->marshalJson(json_encode([
                'id' => $id
            ]));

            // Ejecuta la operación GetItem
            $result = $this->dynamoDb->getItem([
                'TableName' => 'aulas_xxxxxx_iot', // La tabla de AULAS
                'Key' => $key
            ]);

            // Si se encontró el item
            if (isset($result['Item'])) {
                // Decodifica el item
                $item = $this->marshaler->unmarshalValue($result['Item']);

                return [
                    'nombre' => $item['nombreAula'] ?? 'Nombre no encontrado',
                    'campus' => $item['campus'] ?? null
                ];
            }

        } catch (DynamoDbException $e) {
            report($e); // Registra el error
        }

        // Devuelve un valor por defecto en caso de error o si no se encuentra
        return ['nombre' => 'Aula no encontrada (ID: ' . $id . ')', 'campus' => null];
    }
}