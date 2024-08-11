<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        // Ruta del archivo de log
        $logFile = storage_path('logs/laravel.log');

        // Verificar si el archivo existe
        if (File::exists($logFile)) {
            // Leer el contenido del archivo de log
            $logs = File::get($logFile);

            // Dividir el contenido en líneas
            $logLines = explode("\n", $logs);

            // Filtrar las líneas que contienen "ERROR"
            $errorLogs = array_filter($logLines, function ($line) {
                return strpos($line, 'ERROR') !== false;
            });

            // Invertir el orden de las líneas para que los errores más recientes estén primero
            $errorLogs = array_reverse($errorLogs);

            // Convertir cada línea de error en un objeto
            $errorObjects = array_map(function ($line) {
                // Usamos una expresión regular para extraer la fecha, hora y tipo de error
                preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*?)$/', $line, $matches);

                return [
                    'date' => $matches[1] ?? null,
                    'environment' => $matches[2] ?? null,
                    'error_type' => $matches[3] ?? null,
                    'message' => $matches[4] ?? $line,
                ];
            }, $errorLogs);

            // Devolver los logs de errores como objetos
            return response()->json([
                'errors' => array_values($errorObjects) // array_values para resetear los índices del array
            ]);
        }

        return response()->json([
            'message' => 'No logs found.'
        ], 404);
    }
}
