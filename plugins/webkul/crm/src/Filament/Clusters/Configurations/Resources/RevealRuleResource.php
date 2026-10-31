<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\CreateRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\EditRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ListRevealRules;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ManageRevealRuleLeads;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ViewRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Schemas\RevealRuleForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Schemas\RevealRuleInfolist;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Tables\RevealRulesTable;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Services\VisitsToLeads;

class RevealRuleResource extends Resource
{
    protected static ?string $model = RevealRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return VisitsToLeads::enabled() && parent::canViewAny();
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return RevealRuleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RevealRuleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RevealRulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRevealRules::route('/'),
            'create' => CreateRevealRule::route('/create'),
            'view'   => ViewRevealRule::route('/{record}'),
            'edit'   => EditRevealRule::route('/{record}/edit'),
            'leads'  => ManageRevealRuleLeads::route('/{record}/leads'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = $page->generateNavigationItems([
            ViewRevealRule::class,
            EditRevealRule::class,
            ManageRevealRuleLeads::class,
        ]);

        $rule = method_exists($page, 'getRecord') ? $page->getRecord() : null;

        if (! $rule instanceof RevealRule) {
            return $items;
        }

        return array_map(
            fn (NavigationItem $item): NavigationItem => $item->getLabel() === ManageRevealRuleLeads::getNavigationLabel()
                ? $item->badge($rule->leads()->withTrashed()->count())
                : $item,
            $items,
        );
    }
}
