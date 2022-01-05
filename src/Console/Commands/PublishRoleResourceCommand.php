<?php

namespace Reksmey\FilamentSpatieRolesPermissions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishRoleResourceCommand extends Command
{
    public $signature = 'filament-spatie-roles-permissions:publish-role-resource';

    public $description = 'Publish filament RoleResource.';

    public function handle(): int
    {
        $filesystem = (new Filesystem());
        $baseResourcePath = app_path('Filament/Resources');
        $roleResourcePath = $baseResourcePath . '/RoleResource.php';
        $resourceStubsDir = __DIR__ . '/../../../stubs/Resources';
        $pagesPath = $baseResourcePath . '/RoleResource/Pages';

        if ($filesystem->exists($roleResourcePath)) {
            $confirmed = $this->confirm('RoleResource already exists. Overwrite?', false);
            if (!$confirmed) {
                return static::INVALID;
            }
        }

        // publish RoleResource
        $filesystem->ensureDirectoryExists($baseResourcePath);
        $filesystem->copy($resourceStubsDir . '/RoleResource.stub', $roleResourcePath);

        // publish RoleResource pages
        $filesystem->ensureDirectoryExists($pagesPath);
        foreach (['CreateRole', 'EditRole', 'ListRoles'] as $page) {
            $filesystem->copy(
                sprintf("%s/RoleResource/Pages/%s.stub", $resourceStubsDir, $page),
                sprintf('%s/%s.php', $pagesPath, $page)
            );
        }

        $this->info('RoleResource have been published successfully!');

        return static::SUCCESS;
    }
}
