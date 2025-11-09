<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Illuminate\Support\Str; // Para generar IDs

class AulasController extends Controller
{
    protected $dynamoDb;
    protected $marshaler;
    protected $tableName = 'aulas_ecotec_iot';

    public function __construct(DynamoDbClient $dynamoDb)
    {
        $this->dynamoDb = $dynamoDb;
        $this->marshaler = new Marshaler();
    }

    /**
     * Display a listing of the resource.
     * (Muestra la vista de DataTables)
     */
    public function index()
    {
        $aulas = $this->getAllAulas();
        return view('admin.aulas.index', ['aulas' => $aulas]);
    }

    /**
     * Store a newly created resource in storage.
     * (Guarda la nueva aula)
     */
    public function store(Request $request)
    {
        $request->validate([
            'campus' => 'required|string',
            'nombreAula' => 'required|string',
            // 'edificio' es opcional
        ]);

        // Lógica de Edificio
        $edificio = $request->input('edificio', 'N/A');
        if ($request->campus != 'sambo') {
            $edificio = 'N/A'; // Forzamos N/A si no es sambo
        }

        // Preparamos el item para DynamoDB
        $itemData = [
            'id' => random_int(100000, 999999999),
            'campus' => $request->campus,
            'edificio' => $edificio,
            'nombreAula' => $request->nombreAula
        ];

        $item = $this->marshaler->marshalJson(json_encode($itemData));

        try {
            $this->dynamoDb->putItem([
                'TableName' => $this->tableName,
                'Item' => $item
            ]);
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('aulas.index')->with('error', 'Error al crear el aula: ' . $e->getMessage());
        }

        return redirect()->route('aulas.index')->with('success', 'Aula creada exitosamente.');
    }


    /*
     * MÉTODOS DE EDITAR Y BORRAR
     * (Los dejaremos listos para el futuro, pero la vista de hoy se centrará en Crear y Leer)
     */

    public function edit(string $id)
    {
        $aula = $this->getAulaById($id);

        if (!$aula) {
            return response()->json(['error' => 'Aula no encontrada'], 404);
        }

        return response()->json($aula);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'campus' => 'required|string',
            'nombreAula' => 'required|string',
        ]);

        $edificio = $request->input('edificio', 'N/A');
        if ($request->campus != 'sambo') {
            $edificio = 'N/A';
        }

        // Preparamos el item, asegurándonos de usar el ID existente
        // y convertirlo al tipo correcto (número).
        $itemData = [
            'id' => (int) $id, // Usamos el ID de la URL
            'campus' => $request->campus,
            'edificio' => $edificio,
            'nombreAula' => $request->nombreAula
        ];

        $item = $this->marshaler->marshalJson(json_encode($itemData));

        try {
            $this->dynamoDb->putItem([
                'TableName' => $this->tableName,
                'Item' => $item // PutItem sobrescribirá el item con el mismo 'id'
            ]);
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('aulas.index')->with('error', 'Error al actualizar el aula: ' . $e->getMessage());
        }

        return redirect()->route('aulas.index')->with('success', 'Aula actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        try {
            $key = $this->marshaler->marshalJson(json_encode(['id' => (int) $id]));

            $this->dynamoDb->deleteItem([
                'TableName' => $this->tableName,
                'Key' => $key
            ]);

        } catch (\Exception $e) {
            report($e);
            return redirect()->route('aulas.index')->with('error', 'Error al eliminar el aula: ' . $e->getMessage());
        }

        return redirect()->route('aulas.index')->with('success', 'Aula eliminada exitosamente.');
    }
    private function getAulaById(string $id)
    {
        try {
            // El ID debe ser un número para coincidir con tu esquema
            $key = $this->marshaler->marshalJson(json_encode(['id' => (int) $id]));

            $result = $this->dynamoDb->getItem([
                'TableName' => $this->tableName,
                'Key' => $key
            ]);

            if (empty($result['Item'])) {
                return null;
            }

            return $this->unmarshalItem($result['Item']);

        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }


    /**
     * Helper para escanear y obtener todas las aulas.
     */
    private function getAllAulas(): array
    {
        $formattedItems = [];
        $params = ['TableName' => $this->tableName];

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