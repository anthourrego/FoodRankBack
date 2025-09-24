<?php

namespace src\admin\Configuration\Infrastructure\Repositories;

use App\Models\Configuration as ConfigurationModel;
use src\admin\Configuration\Domain\Entities\Configuration;
use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;
use src\admin\Configuration\Domain\ValueObjects\ConfigurationType;
use src\admin\Configuration\Infrastructure\Resources\ConfigurationResource;

class EloquentConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function save(Configuration $configuration): Configuration
    {
        $configurationSaved = ConfigurationModel::create([
            'key' => $configuration->getKey(),
            'value' => $configuration->getValue(),
            'type' => $configuration->getType()->getValue(),
            'description' => $configuration->getDescription(),
        ]);
        
        return $this->mapToEntity($configurationSaved);
    }

    public function findById(int $id): ?Configuration
    {
        $model = ConfigurationModel::find($id);
        
        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByKey(string $key): ?Configuration
    {
        $model = ConfigurationModel::where('key', $key)->first();
        
        return $model ? $this->mapToEntity($model) : null;
    }

    public function findAll(): array
    {
        $models = ConfigurationModel::orderBy('key')->get();
        $configurations = ConfigurationResource::collection($models)->all();
        
        return $configurations;
    }

    public function findByType(string $type): array
    {
        $models = ConfigurationModel::where('type', $type)
            ->orderBy('key')
            ->get();
        
        return $models;
    }

    public function delete(int $id): bool
    {
        return ConfigurationModel::destroy($id) > 0;
    }

    public function updateValue(string $key, string $value): bool
    {
        $model = ConfigurationModel::where('key', $key)->first();
        
        if (!$model) {
            return false;
        }

        $model->value = $value;
        $model->updated_at = new \DateTime();
        
        return $model->save();
    }

    private function mapToEntity(ConfigurationModel $model): Configuration
    {
        return new Configuration(
            id: $model->id,
            key: $model->key,
            value: $model->value,
            type: new ConfigurationType($model->type),
            description: $model->description,
            isActive: $model->is_active,
            createdAt: $model->created_at ? new \DateTime($model->created_at) : null,
            updatedAt: $model->updated_at ? new \DateTime($model->updated_at) : null,
        );
    }

    

}
