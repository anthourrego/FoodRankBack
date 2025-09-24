<?php

namespace src\admin\Configuration\Application\UseCases;

use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;

class GetConfigurations
{
    public function __construct(
        private ConfigurationRepositoryInterface $configurationRepository
    ) {}

    public function execute(?string $type = null): array
    {
        if ($type !== null) {
            return $this->configurationRepository->findByType($type);
        }

        return $this->configurationRepository->findAll();
    }

    public function getByKey(string $key)
    {
        return $this->configurationRepository->findByKey($key);
    }
}
