<?php


namespace src\admin\Configuration\Application\UseCases;

use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;

class ShowConfiguration
{
    public function __construct(
        private ConfigurationRepositoryInterface $configurationRepository
    ) {}

    public function execute(int $eventId): array
    {
        return $this->configurationRepository->findByEventId($eventId);
    }

}
