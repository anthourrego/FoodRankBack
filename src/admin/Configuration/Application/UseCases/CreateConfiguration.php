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
        $value = $request->value;
        
        // Si el tipo es image, manejar la subida del archivo desde imageFile
        if ($request->type === 'image' && $request->hasFile('imageFile')) {
            $file = $request->file('imageFile');
            
            // Generar nombre único para el archivo
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Guardar el archivo en storage/app/public/images
            $filePath = $file->storeAs('images', $fileName, 'public');
            
            // La ruta que se guardará en la base de datos
            $value = 'storage/' . $filePath;
        }

        $configuration = new Configuration(
            id: null,
            key: $request->key,
            value: $value,
            type: new ConfigurationType($request->type),
            description: $request->description,
            createdAt: new \DateTime()
        );

        return $this->configurationRepository->save($configuration);
    }
}
