<?php

namespace Webkul\PluginManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\PluginManager\Models\Plugin;

class PluginSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Plugin::getAllPluginPackages() as $pluginName => $package) {
            $composerPath = $package->basePath('composer.json');

            $composerData = [];

            if (file_exists($composerPath)) {
                $composerData = json_decode(file_get_contents($composerPath), true);
            }

            Plugin::updateOrCreate(
                ['name' => $pluginName],
                [
                    'author'         => $composerData['authors'][0]['name'] ?? 'Webkul',
                    'summary'        => $composerData['description'] ?? $package->description ?? '',
                    'description'    => $composerData['description'] ?? $package->description ?? '',
                    'latest_version' => $composerData['version'] ?? '1.0.0',
                    'license'        => $composerData['license'] ?? 'MIT',
                    'is_active'      => true,
                    'is_installed'   => false,
                    'sort'           => 1,
                ]
            );
        }
    }
}
