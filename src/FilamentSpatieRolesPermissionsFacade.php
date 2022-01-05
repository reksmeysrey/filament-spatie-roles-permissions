<?php

namespace Reksmey\FilamentSpatieRolesPermissions;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getEntities()
 * @method static array getSlugPermissions()
 * @method static array getPermissions()
 */
class FilamentSpatieRolesPermissionsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-spatie-roles-permissions';
    }
}
