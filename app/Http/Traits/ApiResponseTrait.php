<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Retorna una respuesta de éxito con estructura consistente
     *
     * @param string $message Mensaje descriptivo del éxito
     * @param mixed $data Datos a retornar
     * @param int $code Código de respuesta HTTP (por defecto 200)
     * @return JsonResponse
     */
    protected function successResponse(string $message, $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Retorna una respuesta de error con estructura consistente
     *
     * @param string $message Mensaje descriptivo del error
     * @param mixed $data Datos adicionales del error (opcional)
     * @param int $code Código de respuesta HTTP (por defecto 400)
     * @return JsonResponse
     */
    protected function errorResponse(string $message, $data = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

