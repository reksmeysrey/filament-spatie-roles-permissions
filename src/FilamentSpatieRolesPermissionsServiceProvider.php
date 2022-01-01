<?php

namespace Reksmey\FilamentSpatieRolesPermissions;

use Filament\PluginServiceProvider;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource;

class FilamentSpatieRolesPermissionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spatie-roles-and-permissions';

    protected function getResources() :array {
        return [
            RoleResource::class
        ];
    }

    public function bootingPackage()
    {
        if ($this->app->runningInConsole())
        $this->publishes([
            __DIR__ . '/../database/seeders/RolesAndPermissionsSeeder.php' => database_path('seeders/RolesAndPermissionsSeeder.php'),
        ], 'role-permission-seeds');
    }
}
