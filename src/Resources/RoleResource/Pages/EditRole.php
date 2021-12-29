<?php

namespace Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public $permissions;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = Arr::Only($this->data, 'name');
        $data['permissions'] = Arr::except($this->data, ['guard_name', 'id','name', 'select_all', 'created_at', 'updated_at']);

        return $data;
    }

    public function beforeSave()
    {
        $this->permissions = array_filter(Arr::except($this->data, ['guard_name', 'id','name', 'select_all', 'created_at', 'updated_at']));

        $this->permissions = collect(array_keys($this->permissions))->map(fn ($value, $key) => ['name' => $value])->all();
    }


    public function afterSave()
    {
        $this->record->touch();
        $this->record->syncPermissions($this->permissions);
    }


}
