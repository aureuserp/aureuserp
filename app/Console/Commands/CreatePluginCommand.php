<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class CreatePluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin {name : The name of the plugin}
                            {--author=Webkul : The author name}
                            {--description= : The plugin description}
                            {--with-migrations : Include sample migrations}
                            {--with-filament : Include Filament resources}
                            {--admin-panel : Include admin panel resources}
                            {--customer-panel : Include customer panel resources}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plugin with complete structure following blog plugin pattern';

    /**
     * Create a new command instance.
     */
    public function __construct(
        private Filesystem $files
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $pluginName = Str::kebab($name);
        $studlyName = Str::studly($name);
        $pluralName = Str::plural(Str::kebab($name));
        $author = $this->option('author');
        $description = $this->option('description') ?: "Manage {$name} functionality";

        $pluginPath = base_path("plugins/webkul/{$pluralName}");

        if ($this->files->exists($pluginPath)) {
            $this->error("Plugin {$pluralName} already exists!");

            return;
        }

        $this->info("🚀 Creating plugin: {$pluralName}");

        // Create directory structure
        $this->createDirectoryStructure($pluginPath);

        // Create plugin files
        $this->createPluginClass($pluginPath, $studlyName, $pluralName, $description, $author);
        $this->createServiceProvider($pluginPath, $studlyName, $pluralName);

        if ($this->option('with-migrations')) {
            $this->createMigrations($pluginPath, $studlyName, $pluralName);
            $this->createModels($pluginPath, $studlyName, $pluralName);
        }

        if ($this->option('with-filament') || $this->option('admin-panel')) {
            $this->createFilamentResources($pluginPath, $studlyName, $pluralName);
        }

        if ($this->option('customer-panel')) {
            $this->createCustomerResources($pluginPath, $studlyName, $pluralName);
        }

        $this->createComposerJson($pluginPath, $studlyName, $pluralName, $description, $author);
        $this->createPackageJson($pluginPath, $pluralName);
        $this->createAssetFiles($pluginPath, $pluralName);
        $this->createLanguageFiles($pluginPath, $studlyName, $pluralName);

        $this->info("✅ Plugin {$pluralName} created successfully!");

        // Run composer dump-autoload automatically
        $this->info('🔄 Running composer dump-autoload...');
        $this->runComposerDumpAutoload();

        // Register plugin AFTER autoload is complete
        $this->registerPlugin($studlyName, $pluralName);

        $this->info('📋 Next steps:');
        $this->line("   1. Run: php artisan {$pluralName}:install");
        $this->line("   2. Check: plugins/webkul/{$pluralName}");
    }

    private function runComposerDumpAutoload(): void
    {
        $process = new Process(['composer', 'dump-autoload'], base_path());
        $process->setTimeout(120);

        try {
            $process->mustRun();
            $this->info('✅ Composer dump-autoload completed successfully!');
        } catch (\Symfony\Component\Process\ProcessFailedException $exception) {
            $this->error('❌ Failed to run composer dump-autoload:');
            $this->error($exception->getMessage());
            $this->warn("Please run 'composer dump-autoload' manually.");
        }
    }

    private function createDirectoryStructure(string $pluginPath): void
    {
        $directories = [
            'src',
            'src/Filament/Admin/Resources',
            'src/Filament/Customer/Resources',
            'src/Filament/Admin/Clusters',
            'src/Filament/Customer/Clusters',
            'src/Models',
            'src/Policies',
            'database/migrations',
            'database/factories',
            'resources/views',
            'resources/lang/en',
            'resources/js',
            'resources/css',
            'resources/dist',
        ];

        foreach ($directories as $directory) {
            $this->files->makeDirectory("{$pluginPath}/{$directory}", 0755, true);
        }

        // Create additional files
        $this->files->put("{$pluginPath}/.gitignore", "node_modules/\n/dist/\n.env\n");
    }

    private function createPluginClass(string $pluginPath, string $studlyName, string $pluralName, string $description, string $author): void
    {
        $content = "<?php

namespace Webkul\\{$studlyName};

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

class {$studlyName}Plugin implements Plugin
{
    public function getId(): string
    {
        return '{$pluralName}';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel \$panel): void
    {
        if (! Package::isPluginInstalled(\$this->getId())) {
            return;
        }

        \$panel
            ->when(\$panel->getId() == 'customer', function (Panel \$panel) {
                \$panel
                    ->discoverResources(in: \$this->getPluginBasePath('/Filament/Customer/Resources'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Customer\\\\Resources')
                    ->discoverPages(in: \$this->getPluginBasePath('/Filament/Customer/Pages'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Customer\\\\Pages')
                    ->discoverClusters(in: \$this->getPluginBasePath('/Filament/Customer/Clusters'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Customer\\\\Clusters')
                    ->discoverClusters(in: \$this->getPluginBasePath('/Filament/Customer/Widgets'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Customer\\\\Widgets');
            })
            ->when(\$panel->getId() == 'admin', function (Panel \$panel) {
                \$panel
                    ->discoverResources(in: \$this->getPluginBasePath('/Filament/Admin/Resources'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Admin\\\\Resources')
                    ->discoverPages(in: \$this->getPluginBasePath('/Filament/Admin/Pages'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Admin\\\\Pages')
                    ->discoverClusters(in: \$this->getPluginBasePath('/Filament/Admin/Clusters'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Admin\\\\Clusters')
                    ->discoverClusters(in: \$this->getPluginBasePath('/Filament/Admin/Widgets'), for: 'Webkul\\\\{$studlyName}\\\\Filament\\\\Admin\\\\Widgets');
            });
    }

    public function boot(Panel \$panel): void
    {
        //
    }

    protected function getPluginBasePath(\$path = null): string
    {
        \$reflector = new ReflectionClass(get_class(\$this));

        return dirname(\$reflector->getFileName()).(\$path ?? '');
    }
}";

        $this->files->put("{$pluginPath}/src/{$studlyName}Plugin.php", $content);
    }

    private function createServiceProvider(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $migrationDate = now()->format('Y_m_d_His');
        $content = "<?php

namespace Webkul\\{$studlyName};

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class {$studlyName}ServiceProvider extends PackageServiceProvider
{
    public static string \$name = '{$pluralName}';

    public static string \$viewNamespace = '{$pluralName}';

    public function configureCustomPackage(Package \$package): void
    {
        \$package->name(static::\$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '{$migrationDate}_create_{$pluralName}_table',
            ])
            ->runsMigrations()
            ->hasSettings([
            ])
            ->runsSettings()
            ->hasDependencies([
                'website',
            ])
            ->hasInstallCommand(function (InstallCommand \$command) {
                \$command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand \$command) {});
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('{$pluralName}', __DIR__.'/../resources/dist/{$pluralName}.css'),
        ], '{$pluralName}');
    }
}";

        $this->files->put("{$pluginPath}/src/{$studlyName}ServiceProvider.php", $content);
    }

    private function createMigrations(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $migrationDate = now()->format('Y_m_d_His');
        $tableName = $pluralName;

        $content = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->string('slug')->unique();
            \$table->string('image')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->integer('sort_order')->default(0);
            \$table->string('meta_title')->nullable();
            \$table->string('meta_keywords')->nullable();
            \$table->text('meta_description')->nullable();

            \$table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            \$table->softDeletes();
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};";

        $this->files->put("{$pluginPath}/database/migrations/{$migrationDate}_create_{$pluralName}_table.php", $content);
    }

    private function createModels(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $content = "<?php

namespace Webkul\\{$studlyName}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webkul\Security\Models\User;

class {$studlyName} extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected \$table = '{$pluralName}';

    /**
     * Fillable.
     *
     * @var array
     */
    protected \$fillable = [
        'name',
        'description',
        'slug',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'creator_id',
    ];

    /**
     * Casts.
     *
     * @var array
     */
    protected \$casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get image url for the image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (! \$this->image) {
            return null;
        }

        return Storage::url(\$this->image);
    }

    public function creator(): BelongsTo
    {
        return \$this->belongsTo(User::class);
    }
}";

        $this->files->put("{$pluginPath}/src/Models/{$studlyName}.php", $content);
    }

    private function createFilamentResources(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $this->createAdminResource($pluginPath, $studlyName, $pluralName);
        $this->createAdminResourcePages($pluginPath, $studlyName, $pluralName);
    }

    private function createCustomerResources(string $pluginPath, string $studlyName, string $pluralName): void
    {
        // Create similar structure for customer panel if needed
        $this->info('Customer panel resources structure created');
    }

    private function createComposerJson(string $pluginPath, string $studlyName, string $pluralName, string $description, string $author): void
    {
        $content = "{
    \"name\": \"webkul/{$pluralName}\",
    \"description\": \"{$description}\",
    \"type\": \"library\",
    \"license\": \"MIT\",
    \"authors\": [
        {
            \"name\": \"{$author}\",
            \"email\": \"support@webkul.com\"
        }
    ],
    \"require\": {
        \"php\": \"^8.1\"
    },
    \"autoload\": {
        \"psr-4\": {
            \"Webkul\\\\{$studlyName}\\\\\": \"src/\"
        }
    },
    \"extra\": {
        \"laravel\": {
            \"providers\": [
                \"Webkul\\\\{$studlyName}\\\\{$studlyName}ServiceProvider\"
            ]
        }
    },
    \"minimum-stability\": \"dev\",
    \"prefer-stable\": true
}";

        $this->files->put("{$pluginPath}/composer.json", $content);
    }

    private function createPackageJson(string $pluginPath, string $pluralName): void
    {
        $content = '{
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite build --watch"
    },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.2",
        "@tailwindcss/typography": "^0.5.0",
        "autoprefixer": "^10.4.2",
        "axios": "^1.6.4",
        "laravel-vite-plugin": "^1.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.4.0",
        "vite": "^5.0"
    }
}';

        $this->files->put("{$pluginPath}/package.json", $content);
    }

    private function createAssetFiles(string $pluginPath, string $pluralName): void
    {
        // CSS file
        $cssContent = "/* {$pluralName} plugin styles */
.{$pluralName}-container {
    padding: 1rem;
}

.{$pluralName}-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}";

        $this->files->put("{$pluginPath}/resources/css/{$pluralName}.css", $cssContent);
        $this->files->put("{$pluginPath}/resources/dist/{$pluralName}.css", $cssContent);

        // PostCSS config
        $postcssContent = 'export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};';

        $this->files->put("{$pluginPath}/postcss.config.js", $postcssContent);

        // Tailwind config
        $tailwindContent = "/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './src/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};";

        $this->files->put("{$pluginPath}/tailwind.config.js", $tailwindContent);
    }

    private function createLanguageFiles(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $langContent = "<?php

return [
    'navigation' => [
        'title' => '{$studlyName}s',
        'group' => 'Management',
    ],
    'form' => [
        'name' => 'Name',
        'description' => 'Description',
        'image' => 'Image',
        'is_active' => 'Is Active',
        'sort_order' => 'Sort Order',
        'meta_title' => 'Meta Title',
        'meta_keywords' => 'Meta Keywords',
        'meta_description' => 'Meta Description',
    ],
    'table' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'active' => 'Active',
        'created_at' => 'Created At',
    ],
    'messages' => [
        'created' => '{$studlyName} created successfully.',
        'updated' => '{$studlyName} updated successfully.',
        'deleted' => '{$studlyName} deleted successfully.',
    ],
];";

        $this->files->put("{$pluginPath}/resources/lang/en/{$pluralName}.php", $langContent);
    }

    private function createAdminResource(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $content = "<?php

namespace Webkul\\{$studlyName}\Filament\Admin\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages\Create{$studlyName};
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages\Edit{$studlyName};
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages\List{$studlyName}s;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages\View{$studlyName};
use Webkul\\{$studlyName}\Models\\{$studlyName};

class {$studlyName}Resource extends Resource
{
    protected static ?string \$model = {$studlyName}::class;

    protected static ?string \$slug = '{$pluralName}';

    protected static string|\BackedEnum|null \$navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?SubNavigationPosition \$subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string \$recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('<<{$studlyName}s');
    }

    public static function getNavigationGroup(): string
    {
        return __('Management');
    }

    public static function form(Schema \$schema): Schema
    {
        return \$schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('General Information'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (string \$operation, \$state, Set \$set) => \$operation === 'create' ? \$set('slug', Str::slug(\$state)) : null),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique({$studlyName}::class, 'slug', ignoreRecord: true),
                                Textarea::make('description')
                                    ->label(__('Description')),
                                FileUpload::make('image')
                                    ->label(__('Image'))
                                    ->image(),
                                Toggle::make('is_active')
                                    ->label(__('Is Active'))
                                    ->default(true),
                                TextInput::make('sort_order')
                                    ->label(__('Sort Order'))
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Section::make(__('SEO'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->maxLength(255),
                                TextInput::make('meta_keywords')
                                    ->label(__('Meta Keywords'))
                                    ->maxLength(255),
                                Textarea::make('meta_description')
                                    ->label(__('Meta Description')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(2);
    }

    public static function table(Table \$table): Table
    {
        return \$table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('Creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('Active Only')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn (\$record) => \$record->trashed()),
                    EditAction::make()
                        ->hidden(fn (\$record) => \$record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('Restored'))
                                ->body(__('Record has been restored successfully.')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('Deleted'))
                                ->body(__('Record has been deleted successfully.')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('Permanently Deleted'))
                                ->body(__('Record has been permanently deleted.')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema \$schema): Schema
    {
        return \$schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('General Information'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('Name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('description')
                                    ->label(__('Description'))
                                    ->markdown(),
                                ImageEntry::make('image')
                                    ->label(__('Image')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('Details'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('Created By'))
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('Last Updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page \$page): array
    {
        return \$page->generateNavigationItems([
            View{$studlyName}::class,
            Edit{$studlyName}::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => List{$studlyName}s::route('/'),
            'create' => Create{$studlyName}::route('/create'),
            'view'   => View{$studlyName}::route('/{record}'),
            'edit'   => Edit{$studlyName}::route('/{record}/edit'),
        ];
    }
}";

        $this->files->put("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource.php", $content);
    }

    private function createAdminResourcePages(string $pluginPath, string $studlyName, string $pluralName): void
    {
        $this->files->makeDirectory("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource/Pages", 0755, true);

        // List page
        $listContent = "<?php

namespace Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource;

class List{$studlyName}s extends ListRecords
{
    protected static string \$resource = {$studlyName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}";

        // Create page
        $createContent = "<?php

namespace Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource;
use Illuminate\Support\Facades\Auth;

class Create{$studlyName} extends CreateRecord
{
    protected static string \$resource = {$studlyName}Resource::class;

    protected function mutateFormDataBeforeCreate(array \$data): array
    {
        \$data['creator_id'] = Auth::id();

        return \$data;
    }
}";

        // View page
        $viewContent = "<?php

namespace Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource;

class View{$studlyName} extends ViewRecord
{
    protected static string \$resource = {$studlyName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}";

        // Edit page
        $editContent = "<?php

namespace Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webkul\\{$studlyName}\Filament\Admin\Resources\\{$studlyName}Resource;

class Edit{$studlyName} extends EditRecord
{
    protected static string \$resource = {$studlyName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}";

        $this->files->put("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource/Pages/List{$studlyName}s.php", $listContent);
        $this->files->put("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource/Pages/Create{$studlyName}.php", $createContent);
        $this->files->put("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource/Pages/View{$studlyName}.php", $viewContent);
        $this->files->put("{$pluginPath}/src/Filament/Admin/Resources/{$studlyName}Resource/Pages/Edit{$studlyName}.php", $editContent);
    }

    private function registerPlugin(string $studlyName, string $pluralName): void
    {
        // Register in plugins.php
        $pluginsFile = base_path('bootstrap/plugins.php');
        if ($this->files->exists($pluginsFile)) {
            $content = $this->files->get($pluginsFile);
            $newPlugin = "    Webkul\\{$studlyName}\\{$studlyName}Plugin::class,";

            if (! str_contains($content, $newPlugin)) {
                $content = str_replace(
                    '];',
                    "    Webkul\\{$studlyName}\\{$studlyName}Plugin::class,\n];",
                    $content
                );
                $this->files->put($pluginsFile, $content);
                $this->info('✅ Plugin registered in bootstrap/plugins.php');
            }
        }

        // Register in providers.php
        $providersFile = base_path('bootstrap/providers.php');
        if ($this->files->exists($providersFile)) {
            $content = $this->files->get($providersFile);
            $newProvider = "    Webkul\\{$studlyName}\\{$studlyName}ServiceProvider::class,";

            if (! str_contains($content, $newProvider)) {
                $content = str_replace(
                    '];',
                    "    Webkul\\{$studlyName}\\{$studlyName}ServiceProvider::class,\n];",
                    $content
                );
                $this->files->put($providersFile, $content);
                $this->info('✅ Service provider registered in bootstrap/providers.php');
            }
        }
    }
}
