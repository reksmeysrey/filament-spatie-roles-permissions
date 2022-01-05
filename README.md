## Screenshot

![image](https://user-images.githubusercontent.com/35394133/147733275-4de97f59-243c-4b74-958b-534dd70ae4a9.png)

# Description
[![Latest Version on Packagist](https://img.shields.io/packagist/v/reksmey/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/reksmey/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/reskmeysrey/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/reskmey/filament-spatie-roles-permissions)
![GitHub Actions](https://github.com/reksmeysrey/filament-spatie-roles-permissions/actions/workflows/main.yml/badge.svg)

This plugin is built on top of [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package.

## Installation

You can install the package via composer:

```bash
composer require reksmey/filament-spatie-roles-permissions
```

Since the package depends on [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package. You have to publish the migrations by running:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Now you should add any other configurations needed for the Spatie-Permission package.

## Usage

You can add this to your *form* method in your UserResource

```php
return $form->schema([
    ...
    BelongsToManyMultiSelect::make('roles')->relationship('roles', 'name')
    ...
    ...
])

```

## Advance Usage
You can publish permissions seeders in additional permissions. Sometimes you may add more action such as download-pdf, export, etc.

```php
php artisan vendor:publish --tag=filament-spatie-roles-and-permissions-seeders

Then

php artisan db:seeder --class=RolesAndPermissionsSeeder

```

## Customize RoleResource
- publish service provider
    ```php
    php artisan vendor:publish --tag=filament-spatie-roles-and-permissions-provider
    ```
- add the package to the `extra.laravel.dont-discover` key in `composer.json`, e.g.
  ```json
  "extra": {
    "laravel": {
      "dont-discover": [
        "reksmey/filament-spatie-roles-permissions"
      ]
    }
  }
  ```
- publish RoleResource
  ```php
  php artisan filament-spatie-roles-permissions:publish-role-resource
  ```
- you can customize RoleResource in App\Filament
##### [For authorization, Filament will observe any model policies that are registered in your app](https://filamentadmin.com/docs/2.x/admin/resources#authorization)

Hope you enjoy it ❤️

### Security

If you discover any security related issues, please create an issue.

## Credits

-   [bezhanSalleh](https://gist.github.com/bezhanSalleh/bda7d8db237a0c45549b63dafaa387c1)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
