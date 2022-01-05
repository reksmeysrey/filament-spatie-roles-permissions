<?php

namespace Reksmey\FilamentSpatieRolesPermissions;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

class FilamentSpatieRolesPermissions
{
    public function getEntities(): array
    {
        return collect(Filament::getResources())
            ->merge($this->getSlugPermissions())
            ->unique()
            ->reduce(function ($options, $resource) {
                $option = Str::before(Str::afterLast($resource, '\\'), 'Resource');
                $options[$option] = $option;
                return $options;
            }, []);
    }

    public function getSlugPermissions(): array
    {
        return collect(config('filament-permission.permissions', []))
            ->flatten()
            ->values()
            ->map(function (string $action) {
                return Str::slug($action, '_');
            })
            ->all();
    }

    public function getPermissions(): array
    {
        return ['view', 'viewAny', 'create', 'delete', 'deleteAny', 'update'];
    }
}
