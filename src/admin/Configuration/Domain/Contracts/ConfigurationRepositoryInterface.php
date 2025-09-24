<?php

namespace src\admin\Configuration\Domain\Contracts;
use src\admin\Configuration\Domain\Entities\Configuration;

interface ConfigurationRepositoryInterface
{
    public function save(Configuration $configuration): Configuration;
    
    public function findById(int $id): ?Configuration;
    
    public function findByKey(string $key): ?Configuration;
    
    public function findAll(): array;
    
    public function findByType(string $type): array;
    
    public function delete(int $id): bool;
    
    public function updateValue(string $key, string $value): bool;
}
