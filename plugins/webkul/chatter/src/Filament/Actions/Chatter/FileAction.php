<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'file.action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('gray')
            ->outlined()
            ->tooltip(__('chatter::filament/resources/actions/chatter/file-action.setup.tooltip'))
            ->badge(fn ($record) => $record->attachments()->count())
            ->schema([
                FileUpload::make('files')
                    ->hiddenLabel()
                    ->multiple()
                    ->directory('chats-attachments')
                    ->disk('public')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->reorderable()
                    ->previewable(true)
                    ->deletable()
                    ->imagePreviewHeight('100')
                    ->deleteUploadedFileUsing(function ($file, ?Model $record) {
                        if (! $record) {
                            return;
                        }

                        if (method_exists($record, 'removeAttachment')) {
                            $attachment = $record->attachments()->where('file_path', $file)->first();

                            if ($attachment) {
                                $record->removeAttachment($attachment->id);
                            } else {
                                Storage::disk('public')->delete($file);
                            }
                        } else {
                            $attachment = $record->attachments()->where('file_path', $file)->first();

                            if ($attachment) {
                                $attachment->delete();
                            }

                            Storage::disk('public')->delete($file);
                        }

                        Notification::make()
                            ->success()
                            ->title(__('chatter::filament/resources/actions/chatter/file-action.setup.form.fields.actions.delete.title'))
                            ->body(__('chatter::filament/resources/actions/chatter/file-action.setup.form.fields.actions.delete.body'))
                            ->send();
                    })
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
                    ->helperText(__('chatter::filament/resources/actions/chatter/file-action.setup.form.fields.attachment-helper-text'))
                    ->columnSpanFull()
                    ->required()
                    ->default(function (?Model $record) {
                        if (! $record) {
                            return [];
                        }

                        return $record->attachments()
                            ->latest()
                            ->get()
                            ->pluck('file_path')
                            ->toArray() ?? [];
                    }),
            ])
            ->action(function (FileAction $action, array $data, ?Model $record): void {
                try {
                    $existingFiles = $record->attachments()
                        ->latest()
                        ->get()
                        ->pluck('file_path')
                        ->toArray();

                    $newFiles = array_filter($data['files'] ?? [], function ($file) use ($existingFiles) {
                        return ! in_array($file, $existingFiles);
                    });

                    if (! empty($newFiles)) {
                        $record->addAttachments($newFiles);

                        Notification::make()
                            ->success()
                            ->title(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.success.title'))
                            ->body(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.success.body'))
                            ->send();
                    } else {
                        Notification::make()
                            ->info()
                            ->title(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.warning.title'))
                            ->body(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.warning.body'))
                            ->send();
                    }
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.error.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/file-action.setup.actions.notification.error.body'))
                        ->send();

                    report($e);
                }

                $action->resetFormData();
            })
            ->modalHeading(__('chatter::filament/resources/actions/chatter/file-action.setup.title'))
            ->icon('heroicon-o-paper-clip')
            ->modalIcon('heroicon-o-paper-clip')
            ->iconPosition(IconPosition::Before)
            ->after(function ($livewire) {
                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            })
            ->modalSubmitAction(
                fn ($action) => $action
                    ->label('Upload')
                    ->icon('heroicon-m-paper-airplane')
            )
            ->modalWidth(Width::TwoExtraLarge)
            ->slideOver(false);
    }
}
