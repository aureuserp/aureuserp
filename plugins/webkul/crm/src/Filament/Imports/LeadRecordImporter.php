<?php

namespace Webkul\Crm\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Concerns\UsesAbsoluteDownloadUrls;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

abstract class LeadRecordImporter extends Importer
{
    use UsesAbsoluteDownloadUrls;

    protected static ?string $model = Lead::class;

    abstract protected static function getLeadType(): string;

    abstract protected static function getPolicy(): string;

    protected static function leadColumns(): array
    {
        $label = fn (string $key): string => __('crm::filament/transfers.columns.'.$key);

        return [
            ImportColumn::make('id')
                ->label($label('id'))
                ->guess(['id', 'ID', $label('id')])
                ->integer()
                ->fillRecordUsing(fn (): null => null)
                ->ignoreBlankState(),
            ImportColumn::make('name')
                ->label($label('name'))
                ->guess(['name', 'title', $label('name')])
                ->requiredMappingForNewRecordsOnly()
                ->rules(['required', 'max:255'])
                ->example(static::getLeadType() === Lead::TYPE_LEAD ? 'Website enquiry: 50 chairs' : 'Office furniture refresh'),
            ImportColumn::make('contact_name')
                ->label($label('contact-name'))
                ->guess(['contact', 'contact name', $label('contact-name')])
                ->rules(['nullable', 'max:255'])
                ->example('Laura Bennett')
                ->ignoreBlankState(),
            ImportColumn::make('company_name')
                ->label($label('company-name'))
                ->guess(['company', 'company name', $label('company-name')])
                ->rules(['nullable', 'max:255'])
                ->example('Bennett Design Studio')
                ->ignoreBlankState(),
            ImportColumn::make('job_title')
                ->label($label('job-title'))
                ->rules(['nullable', 'max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('email')
                ->label($label('email'))
                ->guess(['email', 'e-mail', $label('email')])
                ->rules(['nullable', 'email', 'max:255'])
                ->example('laura@example.com')
                ->ignoreBlankState(),
            ImportColumn::make('phone')
                ->label($label('phone'))
                ->guess(['phone', 'mobile', $label('phone')])
                ->rules(['nullable', 'max:255'])
                ->example('+1 555 0101')
                ->ignoreBlankState(),
            ImportColumn::make('website')
                ->label($label('website'))
                ->rules(['nullable', 'max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('street1')
                ->label($label('street1'))
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('street2')
                ->label($label('street2'))
                ->rules(['nullable', 'max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('city')
                ->label($label('city'))
                ->rules(['nullable', 'max:255'])
                ->example('Chicago')
                ->ignoreBlankState(),
            ImportColumn::make('zip')
                ->label($label('zip'))
                ->rules(['nullable', 'max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('country')
                ->label($label('country'))
                ->relationship(resolveUsing: fn (?string $state): ?Model => static::resolveCountry($state))
                ->example('United States')
                ->ignoreBlankState(),
            ImportColumn::make('stage')
                ->label($label('stage'))
                ->relationship(resolveUsing: fn (?string $state): ?Model => static::resolveStage($state))
                ->ignoreBlankState()
                ->example('New'),
            ImportColumn::make('user')
                ->label($label('salesperson'))
                ->guess(['salesperson', 'user', 'owner', $label('salesperson')])
                ->relationship(resolveUsing: fn (?string $state, LeadRecordImporter $importer): ?Model => $importer->resolveSalesperson($state))
                ->ignoreBlankState(),
            ImportColumn::make('team')
                ->label($label('team'))
                ->guess(['team', 'sales team', $label('team')])
                ->relationship(resolveUsing: fn (?string $state, LeadRecordImporter $importer): ?Model => $importer->resolveTeam($state))
                ->ignoreBlankState(),
            ImportColumn::make('priority')
                ->label($label('priority'))
                ->castStateUsing(fn (mixed $state): ?OpportunityPriority => static::castPriority($state))
                ->example(OpportunityPriority::Medium->getLabel())
                ->ignoreBlankState(),
            ImportColumn::make('probability')
                ->label($label('probability'))
                ->numeric(decimalPlaces: 2)
                ->rules(['nullable', 'numeric', 'min:0', 'max:100'])
                ->example('20')
                ->ignoreBlankState(),
            ImportColumn::make('tags')
                ->label($label('tags'))
                ->multiple(',')
                ->fillRecordUsing(fn (): null => null)
                ->saveRelationshipsUsing(fn (Lead $record, ?array $state) => $record->tags()->sync(static::resolveTags($state ?? [])))
                ->example('Product, Upsell')
                ->ignoreBlankState(),
            ImportColumn::make('source')
                ->label($label('source'))
                ->relationship(resolveUsing: fn (?string $state): ?Model => static::resolveByName(UTMSource::class, $state, 'source'))
                ->ignoreBlankState(),
            ImportColumn::make('medium')
                ->label($label('medium'))
                ->relationship(resolveUsing: fn (?string $state): ?Model => static::resolveByName(UTMMedium::class, $state, 'medium'))
                ->ignoreBlankState(),
            ImportColumn::make('campaign')
                ->label($label('campaign'))
                ->relationship(resolveUsing: fn (?string $state): ?Model => static::resolveByName(UtmCampaign::class, $state, 'campaign'))
                ->ignoreBlankState(),
            ImportColumn::make('description')
                ->label($label('notes'))
                ->guess(['notes', 'description', $label('notes')])
                ->ignoreBlankState(),
        ];
    }

    public function resolveRecord(): ?Lead
    {
        $id = $this->data['id'] ?? null;

        if (blank($id)) {
            return $this->newRecord();
        }

        $record = Lead::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->where('type', static::getLeadType())
            ->whereIn('company_id', $this->getCompanyIds())
            ->find($id);

        if (! $record) {
            throw new RowImportFailedException(__('crm::filament/transfers.errors.record-not-found', ['id' => $id]));
        }

        $user = $this->getImportUser();

        if (! $user || ! app(static::getPolicy())->update($user, $record)) {
            throw new RowImportFailedException(__('crm::filament/transfers.errors.cannot-update', ['id' => $id]));
        }

        return $record;
    }

    protected function newRecord(): Lead
    {
        $user = $this->getImportUser();

        if (! $user || ! app(static::getPolicy())->create($user)) {
            throw new RowImportFailedException(__('crm::filament/transfers.errors.cannot-create'));
        }

        $record = new Lead;

        $record->forceFill([
            'type'        => static::getLeadType(),
            'company_id'  => $this->getDefaultCompanyId(),
            'creator_id'  => $user?->getKey(),
            'user_id'     => $user?->getKey(),
            'probability' => static::getLeadType() === Lead::TYPE_LEAD ? 10 : 0,
        ]);

        return $record;
    }

    public function getImportUser(): ?User
    {
        $user = $this->getImport()->user;

        return $user instanceof User ? $user : User::query()->find($this->getImport()->user_id);
    }

    public function getCompanyIds(): array
    {
        $user = $this->getImportUser();

        if (! $user) {
            return [];
        }

        return collect([$user->default_company_id])
            ->merge($user->allowedCompanies()->pluck('companies.id'))
            ->filter()
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function getDefaultCompanyId(): ?int
    {
        return $this->getImportUser()?->default_company_id ?? ($this->getCompanyIds()[0] ?? null);
    }

    public function resolveSalesperson(?string $state): ?User
    {
        if (blank($state)) {
            return null;
        }

        $state = trim($state);

        $user = OpportunityForm::scopeSalespeople(User::query(), null)
            ->where(fn (Builder $query): Builder => $query
                ->where('default_company_id', $this->getDefaultCompanyId())
                ->orWhereHas('allowedCompanies', fn (Builder $query): Builder => $query->whereIn('companies.id', $this->getCompanyIds())))
            ->where(fn (Builder $query): Builder => $query->where('email', $state)->orWhere('name', $state))
            ->first();

        return $user ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
            'field' => __('crm::filament/transfers.columns.salesperson'),
            'value' => $state,
        ]));
    }

    public function resolveTeam(?string $state): ?Team
    {
        if (blank($state)) {
            return null;
        }

        $team = Team::query()
            ->withoutGlobalScopes([CompanyScope::class])
            ->where(fn (Builder $query): Builder => $query->whereIn('company_id', $this->getCompanyIds())->orWhereNull('company_id'))
            ->where('name', trim($state))
            ->first();

        return $team ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
            'field' => __('crm::filament/transfers.columns.team'),
            'value' => $state,
        ]));
    }

    protected static function resolveStage(?string $state): ?Stage
    {
        if (blank($state)) {
            return null;
        }

        return Stage::query()->where('name', trim($state))->first()
            ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
                'field' => __('crm::filament/transfers.columns.stage'),
                'value' => $state,
            ]));
    }

    protected static function resolveCountry(?string $state): ?Country
    {
        if (blank($state)) {
            return null;
        }

        $state = trim($state);

        return Country::query()->where('name', $state)->orWhere('code', Str::upper($state))->first()
            ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
                'field' => __('crm::filament/transfers.columns.country'),
                'value' => $state,
            ]));
    }

    protected static function resolveByName(string $model, ?string $state, string $column): ?Model
    {
        if (blank($state)) {
            return null;
        }

        return $model::query()->withoutGlobalScopes([CompanyScope::class])->where('name', trim($state))->first()
            ?? throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
                'field' => __('crm::filament/transfers.columns.'.$column),
                'value' => $state,
            ]));
    }

    protected static function castPriority(mixed $state): ?OpportunityPriority
    {
        if (blank($state)) {
            return null;
        }

        $state = trim((string) $state);

        foreach (OpportunityPriority::cases() as $priority) {
            if (
                (string) $priority->value === $state
                || strcasecmp($priority->name, Str::studly($state)) === 0
                || strcasecmp((string) $priority->getLabel(), $state) === 0
            ) {
                return $priority;
            }
        }

        throw new RowImportFailedException(__('crm::filament/transfers.errors.not-found', [
            'field' => __('crm::filament/transfers.columns.priority'),
            'value' => $state,
        ]));
    }

    protected static function resolveTags(array $names): array
    {
        return collect($names)
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->unique()
            ->map(fn (string $name): int => Tag::query()->firstOrCreate(['name' => $name])->getKey())
            ->values()
            ->all();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('crm::filament/transfers.notifications.import.completed', ['count' => number_format($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.__('crm::filament/transfers.notifications.import.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
