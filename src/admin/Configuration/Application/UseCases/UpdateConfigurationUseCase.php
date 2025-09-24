<?php

namespace src\admin\Configuration\Application\UseCases;

use src\admin\Configuration\Domain\Repositories\ConfigurationRepositoryInterface;
use src\admin\Configuration\Domain\Entities\Configuration;
use src\admin\Configuration\Infrastructure\Validators\UpdateConfigurationRequest;

class UpdateConfigurationUseCase
{
    public function __construct(
        private ConfigurationRepositoryInterface $configurationRepository
    ) {}

    public function execute(UpdateConfigurationRequest $request): Configuration
    {
        $configuration = $this->configurationRepository->findByKey($request->key);
        
        if (!$configuration) {
            throw new \Exception('Configuración no encontrada');
        }


        return $this->configurationRepository->save($configuration);
    }
}
