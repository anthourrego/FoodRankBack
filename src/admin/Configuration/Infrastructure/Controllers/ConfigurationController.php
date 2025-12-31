<?php

namespace src\admin\Configuration\Infrastructure\Controllers;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use src\admin\Configuration\Application\UseCases\CreateConfiguration;
use src\admin\Configuration\Application\UseCases\GetConfigurations;
use src\admin\Configuration\Application\UseCases\ShowConfiguration;
use src\admin\Configuration\Infrastructure\Validators\StoreConfigurationRequest;
use src\admin\Configuration\Infrastructure\Repositories\EloquentConfigurationRepository;

class ConfigurationController
{
    use ApiResponseTrait;
    public function __construct(
        private EloquentConfigurationRepository $configurationRepository

    ) {}

    public function index(Request $request): JsonResponse
    {
        $response = (object) [
            'message' => '',
            'data' => [],
            'success' => false,
        ];
        try {
            $configurations = new GetConfigurations($this->configurationRepository, $request->get('eventId'));
            $configurations = $configurations->execute();

            return response()->json([
                'success' => true,
                'message' => $request->get('eventId'),
                'data' => $configurations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $eventId): JsonResponse
    {
        try {
            $configuration = new ShowConfiguration($this->configurationRepository);
            $configuration = $configuration->execute((int)$eventId);
            if (!$configuration) {
                return $this->errorResponse('Configuración no encontrada', 404);
            }

           return $this->successResponse('Configuración obtenida exitosamente', $configuration);
        } catch (\Exception $e) {
            $message = $e->getMessage() ?? 'Error al obtener la configuración';
            return $this->errorResponse($message, 500);

        }
    }

    public function update(Request $request, string $key): JsonResponse
    {
        /* try {
            $request->validate([
                'value' => 'required|string'
            ]);

            $updateRequest = new \src\admin\Application\DTOs\UpdateConfigurationRequest(
                key: $key,
                value: $request->value
            );

            $configuration = $this->updateConfigurationUseCase->execute($updateRequest);

            return response()->json([
                'success' => true,
                'data' => $configuration,
                'message' => 'Configuración actualizada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } */
    }

    public function store(StoreConfigurationRequest $request)
    {



        try {
            $createConfiguration = new CreateConfiguration($this->configurationRepository);
            $newConfiguration = $createConfiguration->execute($request);


            return $this->successResponse('Configuración creada exitosamente', $newConfiguration, Response::HTTP_CREATED);
        }catch(\Exception $e){
            $message =  $e->getMessage() ?? 'Error al crear la configuración';
            Log::error([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse($message );
        }

    }
}
