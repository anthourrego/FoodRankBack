<?php

namespace src\admin\Configuration\Domain\Contracts;
use src\admin\Configuration\Domain\Entities\Configuration;
use src\admin\Configuration\Infrastructure\Resources\ConfigurationResource;

interface ConfigurationRepositoryInterface
{
    public function save(Configuration $configuration): ?ConfigurationResource;
    
    public function findById(int $id): ?ConfigurationResource;
    
    public function findByKey(string $key): ?ConfigurationResource;
    
    public function findAll(): ?array;
    
    public function findByType(string $type): ?array;
    
    public function delete(int $id): bool;
    
    public function updateValue(string $key, string $value): bool;

    public function findByEventId(int $eventId): ?array;
}
