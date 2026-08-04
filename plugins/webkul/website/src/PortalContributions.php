<?php

namespace Webkul\Website;

use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Filament\Admin\Actions\Portal\PortalAccessActionGroup;
use Webkul\Website\Support\PortalAccess;

class PortalContributions
{
    public static function register(): void
    {
        PartnerSchemaRegistry::table('columns', fn () => [static::portalColumn()]);

        PartnerSchemaRegistry::table('filters.append', fn () => [static::portalConstraint()]);

        PartnerSchemaRegistry::actions('view.header', fn () => [static::headerActionGroup()]);

        PartnerSchemaRegistry::actions('edit.header', fn () => [static::headerActionGroup()]);

        PartnerSchemaRegistry::infolist('general.after', fn () => [static::portalSection()]);
    }

    protected static function headerActionGroup(): ActionGroup
    {
        return PortalAccessActionGroup::make()
            ->button()
            ->color('gray')
            ->icon('heroicon-m-chevron-down')
            ->iconPosition(IconPosition::After)
            ->tooltip(null);
    }

    protected static function portalColumn(): Stack
    {
        return Stack::make([
            TextColumn::make('portal_access')
                ->state(fn (): string => __('website::filament/admin/portal-access.table.columns.portal-access'))
                ->badge()
                ->color('info')
                ->icon('heroicon-o-globe-alt'),
        ])
            ->visible(fn (Partner $record): bool => PortalAccess::isAvailable() && PortalAccess::hasAccess($record));
    }

    protected static function portalConstraint(): Constraint
    {
        return Constraint::make('portal_access')
            ->label(__('website::filament/admin/portal-access.table.filters.portal-access'))
            ->attribute('password')
            ->operators([IsFilledOperator::class])
            ->icon('heroicon-o-globe-alt');
    }

    protected static function portalSection(): Section
    {
        return Section::make(__('website::filament/admin/portal-access.infolist.section.title'))
            ->schema([
                TextEntry::make('portal_access')
                    ->label(__('website::filament/admin/portal-access.infolist.entries.status.label'))
                    ->badge()
                    ->state(fn (Partner $record): string => PortalAccess::hasAccess($record)
                        ? __('website::filament/admin/portal-access.infolist.entries.status.granted')
                        : __('website::filament/admin/portal-access.infolist.entries.status.none'))
                    ->color(fn (Partner $record): string => PortalAccess::hasAccess($record) ? 'success' : 'gray')
                    ->icon('heroicon-o-globe-alt'),
                TextEntry::make('email_verified_at')
                    ->label(__('website::filament/admin/portal-access.infolist.entries.email-verified-at.label'))
                    ->dateTime()
                    ->placeholder(__('website::filament/admin/portal-access.infolist.entries.email-verified-at.placeholder')),
                TextEntry::make('last_login_at')
                    ->label(__('website::filament/admin/portal-access.infolist.entries.last-login-at.label'))
                    ->dateTime()
                    ->placeholder(__('website::filament/admin/portal-access.infolist.entries.last-login-at.placeholder')),
            ])
            ->columns(3)
            ->visible(fn (): bool => PortalAccess::isAvailable());
    }
}
