<?php

namespace src\admin\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use src\admin\Domain\Repositories\SliderRepositoryInterface;
use src\admin\Domain\Repositories\ConfigurationRepositoryInterface;
use src\admin\Infrastructure\Repositories\EloquentSliderRepository;
use src\admin\Infrastructure\Repositories\EloquentConfigurationRepository;
use src\admin\Application\UseCases\Slider\CreateSliderUseCase;
use src\admin\Application\UseCases\Slider\UpdateSliderUseCase;
use src\admin\Application\UseCases\Slider\GetSlidersUseCase;
use src\admin\Application\UseCases\Configuration\UpdateConfigurationUseCase;
use src\admin\Application\UseCases\Configuration\GetConfigurationsUseCase;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositorios
        $this->app->bind(SliderRepositoryInterface::class, EloquentSliderRepository::class);
        $this->app->bind(ConfigurationRepositoryInterface::class, EloquentConfigurationRepository::class);

        // Casos de uso
        $this->app->bind(CreateSliderUseCase::class, function ($app) {
            return new CreateSliderUseCase(
                $app->make(SliderRepositoryInterface::class)
            );
        });

        $this->app->bind(UpdateSliderUseCase::class, function ($app) {
            return new UpdateSliderUseCase(
                $app->make(SliderRepositoryInterface::class)
            );
        });

        $this->app->bind(GetSlidersUseCase::class, function ($app) {
            return new GetSlidersUseCase(
                $app->make(SliderRepositoryInterface::class)
            );
        });

        $this->app->bind(UpdateConfigurationUseCase::class, function ($app) {
            return new UpdateConfigurationUseCase(
                $app->make(ConfigurationRepositoryInterface::class)
            );
        });

        $this->app->bind(GetConfigurationsUseCase::class, function ($app) {
            return new GetConfigurationsUseCase(
                $app->make(ConfigurationRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
