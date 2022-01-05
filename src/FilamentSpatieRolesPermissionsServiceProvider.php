<?php

namespace Reksmey\FilamentSpatieRolesPermissions;

use Filament\PluginServiceProvider;
use Reksmey\FilamentSpatieRolesPermissions\Console\Commands\PublishRoleResourceCommand;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Spatie\LaravelPackageTools\Package;

class FilamentSpatieRolesPermissionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spatie-roles-and-permissions';

    protected function getResources(): array
    {
        return [
            RoleResource::class
        ];
    }

    public function configurePackage(Package $package): void
    {
        $package->hasCommand(PublishRoleResourceCommand::class);

        parent::configurePackage($package);
    }

    public function registeringPackage(): void
    {
        $this->app->bind('filament-spatie-roles-permissions', function (): FilamentSpatieRolesPermissions {
            return new FilamentSpatieRolesPermissions();
        });
    }

    public function bootingPackage()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../stubs/database/seeders/RolesAndPermissionsSeeder.stub') => database_path('seeders/RolesAndPermissionsSeeder.php'),
            ], $this->package->shortName() . '-seeders');

            $this->publishes([
                $this->package->basePath('/../stubs/FilamentSpatieRolesPermissionsServiceProvider.stub') => app_path('Providers/FilamentSpatieRolesPermissionsServiceProvider.php'),
            ], $this->package->shortName() . '-provider');
        }
    }
}
