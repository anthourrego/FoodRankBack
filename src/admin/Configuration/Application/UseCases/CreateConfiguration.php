<?php

namespace src\admin\Configuration\Application\UseCases;

use src\admin\Configuration\Domain\Entities\Configuration;
use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;
use src\admin\Configuration\Domain\ValueObjects\ConfigurationType;
use src\admin\Configuration\Infrastructure\Validators\StoreConfigurationRequest;

class CreateConfiguration
{
    public function __construct(
        private ConfigurationRepositoryInterface $configurationRepository
    ) {}

    public function execute(StoreConfigurationRequest $request)
    {
        
        

        $configuration = new Configuration(
            id: null,
            key: $request->key,
            value: $request->value,
            type: new ConfigurationType($request->type),
            description: $request->description,
            createdAt: new \DateTime()
        );

        return $this->configurationRepository->save($configuration);
    }
}
