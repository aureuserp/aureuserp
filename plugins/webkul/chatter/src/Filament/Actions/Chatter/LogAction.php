<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class LogAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'log.action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('gray')
            ->outlined()
            ->mountUsing(function (Schema $schema) {
                $schema->fill();
            })
            ->schema(
                fn ($form) => $form->schema([
                    Group::make([
                        Actions::make([
                            Action::make('add_subject')
                                ->label(function ($get) {
                                    return $get('showSubject') ? __('chatter::filament/resources/actions/chatter/log-action.setup.form.fields.hide-subject') : __('chatter::filament/resources/actions/chatter/log-action.setup.form.fields.add-subject');
                                })
                                ->action(function ($set, $get) {
                                    if ($get('showSubject')) {
                                        $set('showSubject', false);

                                        return;
                                    }

                                    $set('showSubject', true);
                                })
                                ->link()
                                ->size('sm')
                                ->icon(fn (Get $get) => ! $get('showSubject') ? 'heroicon-s-plus' : 'heroicon-s-minus'),
                        ])
                            ->columnSpan('full')
                            ->alignRight(),
                    ]),
                    TextInput::make('subject')
                        ->placeholder(__('chatter::filament/resources/actions/chatter/log-action.setup.form.fields.subject'))
                        ->live()
                        ->visible(fn ($get) => $get('showSubject'))
                        ->columnSpanFull(),
                    RichEditor::make('body')
                        ->hiddenLabel()
                        ->placeholder(__('chatter::filament/resources/actions/chatter/log-action.setup.form.fields.write-message-here'))
                        ->required()
                        ->fileAttachmentsDirectory('log-attachments')
                        ->columnSpanFull(),
                    FileUpload::make('attachments')
                        ->hiddenLabel()
                        ->multiple()
                        ->directory('log-attachments')
                        ->disk('public')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->previewable(true)
                        ->panelLayout('grid')
                        ->imagePreviewHeight('100')
                        ->acceptedFileTypes([
                            'image/*',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain',
                        ])
                        ->maxSize(10240)
                        ->helperText(__('chatter::filament/resources/actions/chatter/log-action.setup.form.fields.attachments-helper-text'))
                        ->columnSpanFull(),
                    Hidden::make('type')
                        ->default('note'),
                ])
                    ->columns(1)
            )
            ->action(function (array $data, ?Model $record = null) {
                try {
                    $user = filament()->auth()->user();

                    $data['name'] = $record->name;
                    $data['causer_type'] = $user->getMorphClass();
                    $data['causer_id'] = $user->id;
                    $data['is_internal'] = true;

                    $message = $record->addMessage($data, $user->id);

                    if (! empty($data['attachments'])) {
                        $record->addAttachments(
                            $data['attachments'],
                            ['message_id' => $message->id],
                        );
                    }

                    Notification::make()
                        ->success()
                        ->title(__('chatter::filament/resources/actions/chatter/log-action.setup.actions.notification.success.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/log-action.setup.actions.notification.success.body'))
                        ->send();
                } catch (Exception $e) {
                    report($e);
                    Notification::make()
                        ->danger()
                        ->title(__('chatter::filament/resources/actions/chatter/log-action.setup.actions.notification.error.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/log-action.setup.actions.notification.error.body'))
                        ->send();
                }
            })
            ->label(__('chatter::filament/resources/actions/chatter/log-action.setup.title'))
            ->icon('heroicon-o-chat-bubble-oval-left')
            ->modalIcon('heroicon-o-chat-bubble-oval-left')
            ->modalSubmitAction(function ($action) {
                $action->label(__('chatter::filament/resources/actions/chatter/log-action.setup.submit-title'));

                $action->icon('heroicon-m-paper-airplane');
            })
            ->after(function ($livewire) {
                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            })
            ->slideOver(false);
    }
}
