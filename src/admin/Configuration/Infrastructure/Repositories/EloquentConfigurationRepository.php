<?php

namespace src\admin\Configuration\Infrastructure\Repositories;

use App\Models\Configuration as ConfigurationModel;
use src\admin\Configuration\Domain\Entities\Configuration;
use src\admin\Configuration\Domain\Contracts\ConfigurationRepositoryInterface;
use src\admin\Configuration\Infrastructure\Resources\ConfigurationResource;

class EloquentConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function save(Configuration $configuration): ConfigurationResource
    {
        //buscar si el evento ya tiene una configuracion con el mismo key
        $eventHasEqualsKey = ConfigurationModel::where('event_id', $configuration->getEventId())
        ->where('key', $configuration->getKey())
        ->first();

        $configurationSaved = null;
        if($eventHasEqualsKey){
           //actualizar el valor de la configuracion
           $eventHasEqualsKey->update([
            'value' => $configuration->getValue(),
           ]);
           $configurationSaved = $eventHasEqualsKey;
        }else{
            $configurationSaved = ConfigurationModel::create([
                'key' => $configuration->getKey(),
                'value' => $configuration->getValue(),
                'type' => $configuration->getType()->getValue(),
                'description' => $configuration->getDescription(),
                'event_id' => $configuration->getEventId(),
            ]);
        }
        
        return new ConfigurationResource($configurationSaved);
        
    }

    public function findById(int $id): ?ConfigurationResource
    {
        $foundConfig = ConfigurationModel::find($id);

        if($foundConfig){
            return new ConfigurationResource($foundConfig);
        }
        
        return null;
    }

    public function findByKey(string $key): ?ConfigurationResource
    {
        $foundConfig = ConfigurationModel::where('key', $key)->first();

        if($foundConfig){
            return new ConfigurationResource($foundConfig);
        }
        
        return $foundConfig;
    }

    public function findAll(): ?array
    {
        $foundConfigurations = ConfigurationModel::orderBy('key')->with('event')->get();
        if($foundConfigurations){

            return ConfigurationResource::collection($foundConfigurations)->all();
        }
        return $foundConfigurations;
    }

    public function findByType(string $type): ?array
    {
        $configFound = ConfigurationModel::where('type', $type)
            ->orderBy('key')
            ->get();
        
        if($configFound){
            return ConfigurationResource::collection($configFound)->all();
        }
        return $configFound;
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

    public function findByEventId(int $eventId): ?array
    {
        $configFound = ConfigurationModel::where('event_id', $eventId)
            ->orderBy('key')
            ->with('event')
            ->get();
            
        if($configFound){
            return ConfigurationResource::collection($configFound)->all();
        }
        return $configFound;
    }


    

}
