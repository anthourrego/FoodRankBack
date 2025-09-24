<?php

namespace src\admin\Configuration\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use src\admin\Configuration\Application\UseCases\CreateConfiguration;
use src\admin\Configuration\Application\UseCases\GetConfigurations;
use src\admin\Configuration\Infrastructure\Validators\StoreConfigurationRequest;
use src\admin\Configuration\Infrastructure\Repositories\EloquentConfigurationRepository;

class ConfigurationController
{
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
            $configurations = new GetConfigurations($this->configurationRepository);
            $configurations = $configurations->execute();
            
            return response()->json([
                'success' => true,
                'data' => $configurations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $key): JsonResponse
    {
        /* try {
            $configuration = $this->getConfigurationsUseCase->getByKey($key);
            
            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuración no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $configuration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        } */
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
        $response = (object) [
            'message' => '',
            'data' => [],
            'success' => false,
        ];
        
        
        
        try {
            $createConfiguration = new CreateConfiguration($this->configurationRepository);
            $newConfiguration = $createConfiguration->execute($request);
            $response->data = $newConfiguration;
            $response->success = true;
            $response->message = 'Configuración creada exitosamente';

        }catch(\Exception $e){
            $response->message = 'Error al crear la configuración';
            $response->success = false;
            Log::error([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json($response);
    }
}
