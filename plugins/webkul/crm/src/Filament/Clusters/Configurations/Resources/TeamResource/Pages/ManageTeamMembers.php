<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCRMTeam;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageTeamMembers extends ManageRelatedRecords
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs, ManagesCRMTeam {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = TeamResource::class;

    protected static string $relationship = 'crmMembers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/assignment.members.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.members.title', ['team' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/assignment.members.title');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        app(TeamMembership::class)->sync($this->getRecord());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(static::memberFields($this));
    }

    public static function memberFields(self $page, bool $editing = false): array
    {
        $fields = TeamForm::getMemberFields(
            fn (): mixed => $page->getRecord()->company_id,
            fn (): array => TeamMember::query()->where('team_id', $page->getRecord()->getKey())->pluck('user_id')->all(),
            fn (): int => $page->getRecord()->getKey(),
        );

        if (! $editing) {
            return $fields;
        }

        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('member_name')
                        ->label(__('security::filament/resources/user.form.sections.general-information.fields.name'))
                        ->required()
                        ->maxLength(255)
                        ->disabled(fn (?TeamMember $record): bool => ! ($record?->user && UserResource::canEdit($record->user))),
                    TextInput::make('member_email')
                        ->label(__('security::filament/resources/user.form.sections.general-information.fields.email'))
                        ->disabled()
                        ->dehydrated(false),
                ]),
            ...array_filter($fields, fn ($field): bool => ! ($field instanceof Select && $field->getName() === 'user_id')),
        ];
    }

    public function table(Table $table): Table
    {
        $canEdit = fn (): bool => TeamResource::canEdit($this->getRecord());

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('user'))
            ->columns([
                Stack::make([
                    ViewColumn::make('user.name')
                        ->label(__('crm::filament/assignment.members.fields.salesperson'))
                        ->searchable()
                        ->view('crm::filament.teams.member-card'),
                ]),
            ])
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->paginated(false)
            ->recordAction('edit')
            ->headerActions([
                Action::make('addMember')
                    ->label(__('crm::filament/assignment.members.actions.add'))
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading(__('crm::filament/assignment.members.actions.add'))
                    ->modalSubmitActionLabel(__('crm::filament/assignment.members.actions.add'))
                    ->modalWidth('3xl')
                    ->model(TeamMember::class)
                    ->visible($canEdit)
                    ->schema(fn (): array => static::memberFields($this))
                    ->action(function (array $data): void {
                        $removedFrom = app(TeamMembership::class)->addMember($this->getRecord(), (int) $data['user_id'], Arr::except($data, ['user_id']));

                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/assignment.members.notifications.added'))
                            ->body($removedFrom ? __('crm::filament/assignment.members.notifications.removed-from', ['teams' => implode(', ', $removedFrom)]) : null)
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn (TeamMember $record): string => $record->user?->name ?? __('crm::filament/assignment.members.title'))
                    ->modalWidth('3xl')
                    ->visible($canEdit)
                    ->schema(fn (): array => static::memberFields($this, editing: true))
                    ->mutateRecordDataUsing(fn (TeamMember $record, array $data): array => [
                        ...$data,
                        'member_name'  => $record->user?->name,
                        'member_email' => $record->user?->email,
                    ])
                    ->using(function (TeamMember $record, array $data): TeamMember {
                        if (filled($data['member_name'] ?? null) && $record->user && UserResource::canEdit($record->user)) {
                            $record->user->update(['name' => $data['member_name']]);
                        }

                        $record->update(Arr::except($data, ['member_name', 'member_email', 'user_id']));

                        return $record;
                    }),
                DeleteAction::make()
                    ->label(__('crm::filament/assignment.members.actions.remove'))
                    ->modalHeading(__('crm::filament/assignment.members.actions.remove'))
                    ->visible($canEdit)
                    ->using(function (TeamMember $record): bool {
                        app(TeamMembership::class)->removeMember($record);

                        return true;
                    }),
            ])
            ->emptyStateHeading(__('crm::filament/assignment.members.empty'))
            ->emptyStateIcon('heroicon-o-users');
    }
}
