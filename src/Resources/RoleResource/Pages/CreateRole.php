<?php

namespace Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public $permissions;

    public function beforeCreate()
    {
        $this->permissions = array_keys(array_filter(Arr::except($this->data, ['name', 'select_all'])));
    }

    public function afterCreate()
    {
        $_permissions = [];
        foreach ($this->permissions as $name) {
            $_permissions[] = Permission::findOrCreate($name);
        }
        $this->record->syncPermissions($_permissions);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = Arr::Only($this->data, 'name');

        return $data;
    }
}
