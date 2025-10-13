<?php

namespace Webkul\Employee\Filament\Resources;

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
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\CreateDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\EditDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ListDepartments;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ViewDepartment;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages\ManageEmployee;
use Webkul\Employee\Models\Department;
use Webkul\Field\Filament\Traits\HasCustomFields;

class DepartmentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Department::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/department.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/resources/department.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'manager.name', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/resources/department.global-search.name')               => $record->name ?? '—',
            __('employees::filament/resources/department.global-search.department-manager') => $record->manager?->name ?? '—',
            __('employees::filament/resources/department.global-search.company')            => $record->company?->name ?? '—',
        ];
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/resources/department.form.sections.general.title'))
                                    ->schema([
                                        Hidden::make('creator_id')
                                            ->default(Auth::id())
                                            ->required(),
                                        TextInput::make('name')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Select::make('parent_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.parent-department'))
                                            ->relationship(
                                                name: 'parent',
                                                titleAttribute: 'complete_name',
                                                modifyQueryUsing: fn(Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(
                                                fn(Model $record): string => $record->complete_name . ($record->trashed() ? ' (Deleted)' : ''),
                                            )
                                            ->disableOptionWhen(
                                                fn(string $label): bool => str_contains($label, ' (Deleted)'),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('manager_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.manager'))
                                            ->relationship('manager', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.manager-placeholder'))
                                            ->nullable(),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.company'))
                                            ->relationship('company', 'name', modifyQueryUsing: fn(Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                                return $record->name . ($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->preload()
                                            ->searchable()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.company-placeholder'))
                                            ->nullable(),
                                        ColorPicker::make('color')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2)->columnSpanFull(),
                                Section::make(__('employees::filament/resources/department.form.sections.additional.title'))
                                    ->visible(! empty($customFormFields = static::getCustomFormFields()))
                                    ->description(__('employees::filament/resources/department.form.sections.additional.description'))
                                    ->schema($customFormFields),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('manager.partner.avatar')
                        ->height(35)
                        ->circular()
                        ->width(35),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('employees::filament/resources/department.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('manager.name')
                                ->icon('heroicon-m-briefcase')
                                ->label(__('employees::filament/resources/department.table.columns.manager-name'))
                                ->sortable()
                                ->searchable(),
                        ])
                            ->visible(fn($record) => filled($record?->manager?->name)),
                        Stack::make([
                            TextColumn::make('company.name')
                                ->searchable()
                                ->label(__('employees::filament/resources/department.table.columns.company-name'))
                                ->icon('heroicon-m-building-office-2')
                                ->searchable(),
                        ])
                            ->visible(fn($record) => filled($record?->company?->name)),
                    ])->space(1),
                ])->space(4),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/resources/department.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('employees::filament/resources/department.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('manager.name')
                    ->label(__('employees::filament/resources/department.table.groups.manager'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/resources/department.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/resources/department.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(static::mergeCustomTableFilters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/resources/department.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        RelationshipConstraint::make('manager')
                            ->label(__('employees::filament/resources/department.table.filters.manager-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/resources/department.table.filters.manager-name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/resources/department.table.filters.company-name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/resources/department.table.filters.company-name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/resources/department.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/resources/department.table.filters.updated-at')),
                    ]),
            ]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/department.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/department.table.actions.delete.notification.body')),
                    ),
                ActionGroup::make([
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.restore.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/resources/department.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.name')),
                                        TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.manager')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.company')),
                                        ColorEntry::make('color')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.color')),
                                        Fieldset::make(__('employees::filament/resources/department.infolist.sections.general.entries.hierarchy-title'))
                                            ->hidden(fn(Department $record): bool => $record->parent === null)
                                            ->schema([
                                                TextEntry::make('hierarchy')
                                                    ->label('')
                                                    ->html()
                                                    ->state(fn(Department $record): string => static::buildHierarchyTree($record)),
                                            ])->columnSpan('full'),
                                    ])
                                    ->columns(2)->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    protected static function buildHierarchyTree(Department $currentDepartment): string
    {
        $rootDepartment = static::findRootDepartment($currentDepartment);

        return static::renderDepartmentTree($rootDepartment, $currentDepartment);
    }

    protected static function findRootDepartment(Department $department): Department
    {
        $current = $department;

        while ($current->parent_id) {
            $current = $current->parent;
        }

        return $current;
    }

    protected static function renderDepartmentTree(
        Department $department,
        Department $currentDepartment,
        int $depth = 0,
        bool $isLast = true,
        array $parentIsLast = []
    ): string {
        $output = static::formatDepartmentLine(
            $department,
            $depth,
            $department->id === $currentDepartment->id,
            $isLast,
            $parentIsLast
        );

        $children = Department::where('parent_id', $department->id)
            ->where('company_id', $department->company_id)
            ->orderBy('name')
            ->get();

        if ($children->isNotEmpty()) {
            $lastIndex = $children->count() - 1;

            foreach ($children as $index => $child) {
                $newParentIsLast = array_merge($parentIsLast, [$isLast]);

                $output .= static::renderDepartmentTree(
                    $child,
                    $currentDepartment,
                    $depth + 1,
                    $index === $lastIndex,
                    $newParentIsLast
                );
            }
        }

        return $output;
    }

    protected static function formatDepartmentLine(
        Department $department,
        int $depth,
        bool $isActive,
        bool $isLast,
        array $parentIsLast
    ): string {
        $prefix = '';
        if ($depth > 0) {
            for ($i = 0; $i < $depth - 1; $i++) {
                $prefix .= $parentIsLast[$i] ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;';
            }
            $prefix .= $isLast ? '└──&nbsp;' : '├──&nbsp;';
        }

        $employeeCount = $department->employees()->count();
        $managerName = $department->manager?->name ? " · {$department->manager->name}" : '';

        $style = $isActive
            ? 'color: ' . ($department->color ?? '#1D4ED8') . '; font-weight: bold;'
            : '';

        return sprintf(
            '<div class="py-1" style="%s">
                <span class="inline-flex items-center gap-2">
                    %s%s%s
                    <span class="text-sm text-gray-500">
                        (%d members)
                    </span>
                </span>
            </div>',
            $style,
            $prefix,
            e($department->name),
            e($managerName),
            $employeeCount
        );
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/departments';
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDepartment::class,
            EditDepartment::class,
            ManageEmployee::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view'   => ViewDepartment::route('/{record}'),
            'edit'   => EditDepartment::route('/{record}/edit'),
            'employees'  => ManageEmployee::route('/{record}/employees'),

        ];
    }
}
