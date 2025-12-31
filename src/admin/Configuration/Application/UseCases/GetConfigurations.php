<?php

namespace src\admin\Configuration\Application\UseCases;

use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;

class GetConfigurations
{
    public function __construct(
        private ConfigurationRepositoryInterface $configurationRepository,
        private $eventId = null
    ) {}

    public function execute(?string $type = null): array
    {
        if ($type !== null) {
            return $this->configurationRepository->findByType($type);
        }

        if (!is_null($this->eventId)) {
            return $this->configurationRepository->findByEventId((int)$this->eventId);
        }

        return $this->configurationRepository->findAll();
    }

    public function getByKey(string $key)
    {
        return $this->configurationRepository->findByKey($key);
    }
}
