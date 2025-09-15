<?php

namespace Webkul\Employee\Traits\Resources\Employee;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\ResumeDisplayType;

trait EmployeeResumeRelation
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->label('Title')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.title'))
                        ->required()
                        ->reactive(),
                    Select::make('type')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.type'))
                        ->relationship(name: 'resumeType', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Group::make()
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true),
                                    Hidden::make('creator_id')
                                        ->default(Auth::user()->id)
                                        ->required(),
                                ])->columns(2),
                        ])
                        ->createOptionAction(function (Action $action) {
                            return $action
                                ->modalHeading(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.create-type'))
                                ->modalSubmitActionLabel(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.create-type'))
                                ->modalWidth('2xl');
                        }),
                    Fieldset::make(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.duration'))
                        ->schema([
                            DatePicker::make('start_date')
                                ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.start-date'))
                                ->required()
                                ->native(false)
                                ->reactive(),
                            Forms\Components\Datepicker::make('end_date')
                                ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.end-date'))
                                ->native(false)
                                ->reactive(),
                        ]),
                    Select::make('display_type')
                        ->preload()
                        ->options(ResumeDisplayType::options())
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.display-type'))
                        ->searchable()
                        ->required()
                        ->reactive(),
                    Textarea::make('description')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.description')),
                ])->columns(2)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.title'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.start-date'))
                    ->sortable()
                    ->toggleable()
                    ->date(),
                TextColumn::make('end_date')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.end-date'))
                    ->sortable()
                    ->toggleable()
                    ->date(),
                TextColumn::make('display_type')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.display-type'))
                    ->default(fn ($record) => ResumeDisplayType::options()[$record->display_type])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.description'))
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.created-at'))
                    ->sortable()
                    ->toggleable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('resumeType.name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.group-by-type'))
                    ->collapsible(),

                Tables\Grouping\Group::make('display_type')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.group-by-display-type'))
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type_id')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.type'))
                    ->relationship('resumeType', 'name')
                    ->searchable(),
                Filter::make('start_date')
                    ->schema([
                        DatePicker::make('start')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.start-date-from')),
                        DatePicker::make('end')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.start-date-to')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['start'],
                                fn ($query, $start) => $query->whereDate('start_date', '>=', $start)
                            )
                            ->when(
                                $data['end'],
                                fn ($query, $end) => $query->whereDate('start_date', '<=', $end)
                            );
                    }),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.created-from')),
                        DatePicker::make('to')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.created-to')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $from) => $query->whereDate('created_at', '>=', $from)
                            )
                            ->when(
                                $data['to'],
                                fn ($query, $to) => $query->whereDate('created_at', '<=', $to)
                            );
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.header-actions.add-resume'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::user()->id;
                        $data['user_id'] = Auth::user()->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/relation-manager/resume.table.actions.create.notification.title'))
                            ->body(__('employees::filament/resources/employee/relation-manager/resume.table.actions.create.notification.body'))
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/relation-manager/resume.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/resources/employee/relation-manager/resume.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/relation-manager/resume.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/employee/relation-manager/resume.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee/relation-manager/resume.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/employee/relation-manager/resume.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.title'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('display_type')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.display-type'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-document'),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('resumeType.name')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.type')),
                                    ]),
                                TextEntry::make('description')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.description')),
                            ])->columns(2),
                        Fieldset::make(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.duration'))
                            ->schema([
                                TextEntry::make('start_date')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.start-date'))
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('end_date')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.end-date'))
                                    ->icon('heroicon-o-calendar'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
