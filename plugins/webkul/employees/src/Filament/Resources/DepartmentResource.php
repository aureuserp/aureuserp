<?php

namespace Webkul\Employee\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Resources\DepartmentResource\Pages;
use Webkul\Employee\Models\Department;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Models\Company;

class DepartmentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('employees::filament/resources/department.form.sections.general.title'))
                                    ->schema([
                                        Forms\Components\Hidden::make('creator_id')
                                            ->default(Auth::id())
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Forms\Components\Select::make('parent_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.parent-department'))
                                            ->relationship('parent', 'complete_name')
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: true),
                                        Forms\Components\Select::make('manager_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.manager'))
                                            ->relationship('manager', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.manager-placeholder'))
                                            ->nullable(),
                                        Forms\Components\Select::make('company_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->options(fn () => Company::pluck('name', 'id'))
                                            ->searchable()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.company-placeholder'))
                                            ->nullable(),
                                        Forms\Components\ColorPicker::make('color')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2),
                                Forms\Components\Section::make(__('employees::filament/resources/department.form.sections.additional.title'))
                                    ->visible(! empty($customFormFields = static::getCustomFormFields()))
                                    ->description(__('employees::filament/resources/department.form.sections.additional.description'))
                                    ->schema($customFormFields),
                            ]),
                    ]),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('manager.partner.avatar')
                        ->height(35)
                        ->circular()
                        ->width(35),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('employees::filament/resources/department.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('manager.name')
                                ->icon('heroicon-m-briefcase')
                                ->label(__('employees::filament/resources/department.table.columns.manager-name'))
                                ->sortable()
                                ->searchable(),
                        ])
                            ->visible(fn ($record) => filled($record?->manager?->name)),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('company.name')
                                ->searchable()
                                ->label(__('employees::filament/resources/department.table.columns.company-name'))
                                ->icon('heroicon-m-building-office-2')
                                ->searchable(),
                        ])
                            ->visible(fn ($record) => filled($record?->company?->name)),
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
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('employees::filament/resources/department.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('manager')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('employees::filament/resources/department.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('employees::filament/resources/department.table.filters.updated-at')),
                    ]),
            ]))
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/department.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/department.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.restore.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('employees::filament/resources/department.infolist.sections.general.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.name')),
                                        Infolists\Components\TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.manager')),
                                        Infolists\Components\TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.company')),
                                        Infolists\Components\ColorEntry::make('color')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/department.infolist.sections.general.entries.color')),
                                        Infolists\Components\Fieldset::make(__('employees::filament/resources/department.infolist.sections.general.entries.hierarchy-title'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('hierarchy')
                                                    ->label('')
                                                    ->html()
                                                    ->state(fn (Department $record): string => static::buildHierarchyTree($record)),
                                            ])->columnSpan('full'),
                                    ])
                                    ->columns(2),
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
            ? 'color: '.($department->color ?? '#1D4ED8').'; font-weight: bold;'
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

    public static function getSlug(): string
    {
        return 'employees/departments';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view'   => Pages\ViewDepartment::route('/{record}'),
            'edit'   => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
