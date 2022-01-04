<?php

namespace Reksmey\FilamentSpatieRolesPermissions\Resources;

use Closure;
use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Tables\Columns;
use Filament\Resources\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Reksmey\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

class RoleResource extends Resource
{
    public static function getModel(): string
    {
        return config('permission.models.role', Role::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getLabel(): string
    {
        return __('filament-spatie-roles-and-permissions::filament-spatie.section.role');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-spatie-roles-and-permissions::filament-spatie.section.roles_and_permissions');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-spatie-roles-and-permissions::filament-spatie.section.roles');
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-spatie-roles-and-permissions::filament-spatie.section.roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Role::class, 'name', fn($record) => $record),
                                Forms\Components\Select::make('guard_name')
                                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.guard_name'))
                                    ->nullable()
                                    ->options(function (): array {
                                        $guards = array_keys(config('auth.guards', []));

                                        return array_combine($guards, $guards);
                                    })
                                    ->default(config('auth.defaults.guard')),
                                Forms\Components\Toggle::make('select_all')
                                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.select_all'))
                                    ->helperText(__('filament-spatie-roles-and-permissions::filament-spatie.message.select_all'))
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        foreach (static::getEntities() as $entity) {
                                            $set($entity, $state);

                                            foreach (static::getPermissions() as $perm) {
                                                $set($entity . '_' . $perm, $state);
                                            }
                                        }
                                    })
                            ]),
                    ]),
                Forms\Components\Grid::make([
                    'sm' => 2,
                    'lg' => 3,
                ])
                    ->schema(static::getEntitySchema())
                    ->columns([
                        'sm' => 2,
                        'lg' => 3
                    ])
            ]);
    }

    protected static function getEntities(): array
    {
        return collect(Filament::getResources())
            ->merge(static::getSlugPermissions())
            ->unique()
            ->reduce(function ($options, $resource) {
                $option = Str::before(Str::afterLast($resource, '\\'), 'Resource');
                $options[$option] = $option;
                return $options;
            }, []);
    }

    protected static function getPermissions(): array
    {
        return ['view', 'viewAny', 'create', 'delete', 'deleteAny', 'update'];
    }

    protected static function getEntitySchema(): array
    {
        return collect(static::getEntities())->reduce(function (array $entities, string $entity) {
            $entities[] = Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Toggle::make($entity)
                        ->label(__($entity))
                        ->onIcon('heroicon-s-lock-open')
                        ->offIcon('heroicon-s-lock-closed')
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, Closure $get, bool $state) use ($entity) {
                            collect(static::getPermissions())
                                ->each(function (string $permission) use ($set, $entity, $state) {
                                    $set($entity . '_' . $permission, $state);
                                });

                            if (!$state) {
                                $set('select_all', false);
                            }

                            static::freshSelectAll($get, $set);
                        }),
                    Forms\Components\Fieldset::make('Permissions')
                        ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.permissions'))
                        ->extraAttributes(['class' => 'text-primary-600', 'style' => 'border-color:var(--primary)'])
                        ->columns([
                            'default' => 2,
                            'xl' => 3
                        ])
                        ->schema(static::getPermissionsSchema($entity))
                ])
                ->columns(2)
                ->columnSpan(1);
            return $entities;
        }, []);
    }

    protected static function getPermissionsSchema(string $entity): array
    {
        return collect(static::getPermissions())->reduce(function (array $permissions, string $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($entity . '_' . $permission)
                ->label(__($permission))
                ->extraAttributes(['class' => 'text-primary-600'])
                ->afterStateHydrated(function (Closure $set, Closure $get, ?Role $record) use ($entity, $permission) {
                    if (is_null($record)) return;

                    $existed = $record->checkPermissionTo($entity . '_' . $permission);
                    $existed_Module = $record->checkPermissionTo($entity);

                    if ($existed) {
                        $set($entity . '_' . $permission, $existed);
                    }

                    if ($existed_Module) {
                        $set($entity, true);
                    } else {
                        $set($entity, false);
                        $set('select_all', false);
                    }

                    static::freshSelectAll($get, $set);
                })
                ->reactive()
                ->afterStateUpdated(function (Closure $set, Closure $get, bool $state) use ($entity) {
                    $permissionStates = [];
                    foreach (static::getPermissions() as $perm) {
                        $permissionStates [] = $get($entity . '_' . $perm);
                    }

                    if (in_array(false, $permissionStates, true) === false) {
                        $set($entity, true); // if all permissions true => turn toggle on
                    }

                    if (in_array(false, $permissionStates, true) === true) {
                        $set($entity, false); // if even one false => turn toggle off
                    }

                    if (!$state) {
                        $set($entity, false);
                        $set('select_all', false);
                    }

                    static::freshSelectAll($get, $set);
                });
            return $permissions;
        }, []);
    }

    protected static function getSlugPermissions(): array
    {
        return collect(config('filament-permission.permissions', []))
            ->flatten()
            ->values()
            ->map(function (string $action) {
                return Str::slug($action, '_');
            })
            ->all();
    }

    protected static function freshSelectAll(Closure $get, Closure $set): void
    {
        $entityStates = collect(static::getEntities())
            ->map(fn(string $entity): bool => (bool)$get($entity));

        if ($entityStates->containsStrict(false) === false) {
            $set('select_all', true); // if all toggles on => turn select_all on
        }

        if ($entityStates->containsStrict(false) === true) {
            $set('select_all', false); // if even one toggle off => turn select_all off
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('name')
                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.name'))
                    ->sortable()
                    ->searchable(),
                Columns\TextColumn::make('created_at')
                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.created_at'))
                    ->sortable()
                    ->searchable(),
                Columns\TextColumn::make('updated_at')
                    ->label(__('filament-spatie-roles-and-permissions::filament-spatie.field.updated_at'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
