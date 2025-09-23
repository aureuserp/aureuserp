<?php

namespace Webkul\ProductReview\Filament\Admin\Resources;

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
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages\CreateProductReview;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages\EditProductReview;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages\ListProductReviews;
use Webkul\ProductReview\Filament\Admin\Resources\ProductReviewResource\Pages\ViewProductReview;
use Webkul\ProductReview\Models\ProductReview;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $slug = 'product-reviews';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('CRUD ProductReviews');
    }

    public static function getNavigationGroup(): string
    {
        return __('Management');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
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
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ProductReview::class, 'slug', ignoreRecord: true),
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

    public static function table(Table $table): Table
    {
        return $table
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
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProductReview::class,
            EditProductReview::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProductReviews::route('/'),
            'create' => CreateProductReview::route('/create'),
            'view'   => ViewProductReview::route('/{record}'),
            'edit'   => EditProductReview::route('/{record}/edit'),
        ];
    }
}