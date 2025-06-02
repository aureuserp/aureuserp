<?php

namespace Webkul\Employee\Traits\Resources\Employee;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums;

trait EmployeeResumeRelation
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Title')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.title'))
                        ->required()
                        ->reactive(),
                    Forms\Components\Select::make('type')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.type'))
                        ->relationship(name: 'resumeType', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true),
                                    Forms\Components\Hidden::make('creator_id')
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
                    Forms\Components\Fieldset::make(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.duration'))
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.start-date'))
                                ->required()
                                ->native(false)
                                ->reactive(),
                            Forms\Components\Datepicker::make('end_date')
                                ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.end-date'))
                                ->native(false)
                                ->reactive(),
                        ]),
                    Forms\Components\Select::make('display_type')
                        ->preload()
                        ->options(Enums\ResumeDisplayType::options())
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.display-type'))
                        ->searchable()
                        ->required()
                        ->reactive(),
                    Forms\Components\Textarea::make('description')
                        ->label(__('employees::filament/resources/employee/relation-manager/resume.form.sections.fields.description')),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.title'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.start-date'))
                    ->sortable()
                    ->toggleable()
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.end-date'))
                    ->sortable()
                    ->toggleable()
                    ->date(),
                Tables\Columns\TextColumn::make('display_type')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.display-type'))
                    ->default(fn ($record) => Enums\ResumeDisplayType::options()[$record->display_type])
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.description'))
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.created-by'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.created-at'))
                    ->sortable()
                    ->toggleable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('type.name')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.group-by-type'))
                    ->collapsible(),

                Tables\Grouping\Group::make('display_type')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.group-by-display-type'))
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_id')
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.type'))
                    ->relationship('resumeType', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.start-date-from')),
                        Forms\Components\DatePicker::make('end')
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

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('employees::filament/resources/employee/relation-manager/resume.table.groups.created-from')),
                        Forms\Components\DatePicker::make('to')
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
                Tables\Actions\CreateAction::make()
                    ->label(__('employees::filament/resources/employee/relation-manager/resume.table.header-actions.add-resume'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/relation-manager/resume.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/resources/employee/relation-manager/resume.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/relation-manager/resume.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/employee/relation-manager/resume.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/employee/relation-manager/resume.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/employee/relation-manager/resume.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.title'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-document-text'),
                                Infolists\Components\TextEntry::make('display_type')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.display-type'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-document'),
                                Infolists\Components\Group::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('resumeType.name')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.type')),
                                    ]),
                                Infolists\Components\TextEntry::make('description')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.description')),
                            ])->columns(2),
                        Infolists\Components\Fieldset::make(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.duration'))
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee/relation-manager/resume.infolist.entries.start-date'))
                                    ->icon('heroicon-o-calendar'),
                                Infolists\Components\TextEntry::make('end_date')
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
