<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Webkul\Chatter\Mail\FollowerMail;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Services\EmailService;

class FollowerAction extends Action
{
    protected string $mailView = 'chatter::mail.follower-mail';

    protected string $resource = '';

    public static function getDefaultName(): ?string
    {
        return 'add.followers.action';
    }

    public function setResource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function setFollowerMailView(?string $mailView): self
    {
        $mailView = $this->evaluate($mailView);

        if (empty($mailView)) {
            return $this;
        }

        $this->mailView = $mailView;

        return $this;
    }

    public function getFollowerMailView(): string
    {
        return $this->mailView;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-s-user')
            ->color('gray')
            ->modal()
            ->tooltip(__('chatter::filament/resources/actions/chatter/follower-action.setup.tooltip'))
            ->modalIcon('heroicon-s-user-plus')
            ->badge(fn (Model $record): int => $record->followers()->count())
            ->modalWidth('2xl')
            ->slideOver(false)
            ->schema(function ($schema) {
                return $schema
                    ->components([
                        Select::make('partners')
                            ->label(__('chatter::filament/resources/actions/chatter/follower-action.setup.form.fields.recipients'))
                            ->multiple()
                            ->preload()
                            ->live()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Partner::query()
                                    ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%"))
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->getOptionLabelsUsing(function (array $values) {
                                return Partner::query()
                                    ->whereIn('id', $values)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required(),
                        Toggle::make('notify')
                            ->live()
                            ->label(__('chatter::filament/resources/actions/chatter/follower-action.setup.form.fields.notify-user')),
                        RichEditor::make('note')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->visible(fn ($get) => $get('notify'))
                            ->hiddenLabel()
                            ->placeholder(__('chatter::filament/resources/actions/chatter/follower-action.setup.form.fields.add-a-note')),
                    ])
                    ->columns(1);
            })
            ->modalContentFooter(function (Model $record) {
                return view('chatter::filament.actions.follower-action', [
                    'record' => $record,
                ]);
            })
            ->action(function (Model $record, array $data) {

                try {
                    collect($data['partners'] ?? [])->each(function ($partnerId) use ($record, $data) {
                        $partner = Partner::findOrFail($partnerId);

                        $record->addFollower($partner);

                        if (
                            ! empty($data['notify'])
                            && $data['notify']
                        ) {
                            $this->notifyFollower($record, $partner, $data);
                        }

                        // Refresh relation to show immediately in the modal
                        try {
                            $record->unsetRelation('followers');
                        } catch (\Throwable $e) {
                        }

                        Notification::make()
                            ->success()
                            ->title(__('chatter::filament/resources/actions/chatter/follower-action.setup.actions.notification.success.title'))
                            ->body(__('chatter::filament/resources/actions/chatter/follower-action.setup.actions.notification.success.body', ['partner' => $partner->name]))
                            ->send();
                    });
                } catch (Throwable $e) {
                    info('Error adding followers', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    Notification::make()
                        ->danger()
                        ->title(__('chatter::filament/resources/actions/chatter/follower-action.setup.actions.notification.error.title'))
                        ->body(__('chatter::filament/resources/actions/chatter/follower-action.setup.actions.notification.error.body'))
                        ->send();
                }
            })
            ->hiddenLabel()
            ->modalHeading(__('chatter::filament/resources/actions/chatter/follower-action.setup.title'))
            ->after(function ($livewire) {
                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            })
            ->modalSubmitAction(
                fn ($action) => $action
                    ->label(__('chatter::filament/resources/actions/chatter/follower-action.setup.submit-action-title'))
                    ->icon('heroicon-m-user-plus')
            );
    }

    private function notifyFollower(Model $record, Partner $partner, array $data): void
    {
        app(EmailService::class)->send(
            mailClass: FollowerMail::class,
            view: $this->getFollowerMailView(),
            payload: $this->preparePayload($record, $partner, $data),
        );
    }

    private function prepareResourceUrl(mixed $record): string
    {
        return $this->getResource()::getUrl('view', ['record' => $record]);
    }

    public function preparePayload(Model $record, Partner $partner, $data): array
    {
        return [
            'record_url'     => $this->prepareResourceUrl($record) ?? '',
            'record_name'    => $recordName = $record->{$record->recordTitleAttribute} ?? $record->name,
            'model_name'     => $modelName = class_basename($record),
            'subject'        => __('chatter::filament/resources/actions/chatter/follower-action.setup.actions.mail.subject', [
                'model'      => $modelName,
                'department' => $recordName,
            ]),
            'note'           => $data['note'] ?? '',
            'to'             => [
                'address' => $partner->email,
                'name'    => $partner->name,
            ],
        ];
    }
}
