<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations;

class ManageLanguages extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $slug = 'languages';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static ?int $navigationSort = 8;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_languages';
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/pages/manage-languages.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/pages/manage-languages.navigation.title');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/configurations/pages/manage-languages.title');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => collect(config('app.supported_locales', []))
                ->map(fn (array $locale, string $code): array => [
                    'code'       => $code,
                    'label'      => $locale['label'] ?? $code,
                    'native'     => $locale['native'] ?? $code,
                    'rtl'        => (bool) ($locale['rtl'] ?? false),
                    'is_default' => $code === config('app.locale'),
                ])
                ->all())
            ->columns([
                TextColumn::make('label')
                    ->label(__('crm::filament/clusters/configurations/pages/manage-languages.table.columns.name')),
                TextColumn::make('native')
                    ->label(__('crm::filament/clusters/configurations/pages/manage-languages.table.columns.native-name')),
                TextColumn::make('code')
                    ->label(__('crm::filament/clusters/configurations/pages/manage-languages.table.columns.code'))
                    ->badge(),
                IconColumn::make('rtl')
                    ->label(__('crm::filament/clusters/configurations/pages/manage-languages.table.columns.rtl'))
                    ->boolean(),
                IconColumn::make('is_default')
                    ->label(__('crm::filament/clusters/configurations/pages/manage-languages.table.columns.default'))
                    ->boolean(),
            ])
            ->paginated(false);
    }
}
