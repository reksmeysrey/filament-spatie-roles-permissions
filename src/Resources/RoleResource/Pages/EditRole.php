<?php

namespace Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public array $permissions;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = static::onlyPermissionsKeys($data);

        return Arr::Only($data, 'name');
    }

    public function afterSave(): void
    {
        $permissions = [];
        foreach ($this->permissions as $name) {
            $permissions[] = Permission::findOrCreate($name);
        }

        $this->record->touch();
        $this->record->syncPermissions($permissions);
    }

    public static function onlyPermissionsKeys($data): array
    {
        return array_keys(array_filter(Arr::except($data, ['guard_name', 'id', 'name', 'select_all', 'created_at', 'updated_at'])));
    }
}
