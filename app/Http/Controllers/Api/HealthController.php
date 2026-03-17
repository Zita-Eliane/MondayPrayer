<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class HealthController
{
    /**
     * @OA\Get(
     *     path="/api/health",
     *     operationId="apiHealth",
     *     tags={"System"},
     *     summary="Vérifie l'état de l'API",
     *     description="Endpoint simple de vérification de disponibilité.",
     *     @OA\Response(
     *         response=200,
     *         description="API disponible",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="service", type="string", example="MondayPrayer API"),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'MondayPrayer API',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
