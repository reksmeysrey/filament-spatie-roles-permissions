<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(static::getPermissionNames())->each(fn ($permission) => Permission::findOrCreate($permission));
    }

    protected static function getSlugPermissions(): ?array
    {
        return array_map(function($action){
            return Str::slug($action, '_');
        }, collect(config('filament-permission.permissions', []))->flatten()->values()->all());
    }

    protected static function getEntities(): ?array
    {
        return collect(Filament::getResources())
            ->merge(self::getSlugPermissions())//Cut a Space out and replace with '_'
            ->unique()
            ->reduce(function ($options, $resource) {
                $option = Str::before(Str::afterLast($resource,'\\'),'Resource');
                $options[$option] = $option;

                return $options;
            }, []);
    }

    protected static function getPermissions(): array
    {
        return ['view','viewAny','create','delete','deleteAny','update'];
    }

    public static function getPermissionNames(): ?array
    {
        return collect(static::getEntities())->map(function($entity) {
            return collect(static::getPermissions())
                    ->map(function ($permission) use ($entity) {
                        return $entity.'_'.$permission;
                    },[])->all();
        },[])->flatten()->values()->all();
    }
}
