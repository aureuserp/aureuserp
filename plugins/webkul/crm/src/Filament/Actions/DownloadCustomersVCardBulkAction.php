<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

class DownloadCustomersVCardBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'downloadCustomersVCard';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/customer.actions.vcard.label'))
            ->icon('heroicon-o-identification')
            ->deselectRecordsAfterCompletion()
            ->action(fn (Collection $records): StreamedResponse => response()->streamDownload(
                function () use ($records): void {
                    echo $records->map(fn (Partner $contact): string => static::vCard($contact))->implode('');
                },
                $records->count() === 1 ? str($records->first()->name)->slug().'.vcf' : 'contacts.vcf',
                ['Content-Type' => 'text/vcard; charset=utf-8'],
            ));
    }

    public static function vCard(Partner $contact): string
    {
        $contact->loadMissing(['parent', 'country', 'state']);

        $isCompany = $contact->account_type === AccountType::COMPANY;
        $organization = $isCompany ? $contact->name : $contact->parent?->name;

        $lines = array_filter([
            'BEGIN:VCARD',
            'VERSION:3.0',
            'N:'.static::escape($contact->name).';;;;',
            'FN:'.static::escape($contact->name),
            $organization ? 'ORG:'.static::escape($organization) : null,
            filled($contact->job_title) ? 'TITLE:'.static::escape($contact->job_title) : null,
            filled($contact->email) ? 'EMAIL;TYPE=INTERNET:'.static::escape($contact->email) : null,
            filled($contact->phone) ? 'TEL;TYPE=WORK,VOICE:'.static::escape($contact->phone) : null,
            filled($contact->mobile) ? 'TEL;TYPE=CELL:'.static::escape($contact->mobile) : null,
            filled($contact->website) ? 'URL:'.static::escape($contact->website) : null,
            collect([$contact->street1, $contact->street2, $contact->city, $contact->zip])->filter()->isNotEmpty() || $contact->country
                ? 'ADR;TYPE=WORK:;;'.implode(';', array_map(fn (?string $part): string => static::escape((string) $part), [
                    collect([$contact->street1, $contact->street2])->filter()->implode(', '),
                    $contact->city,
                    $contact->state?->name,
                    $contact->zip,
                    $contact->country?->name,
                ]))
                : null,
            'END:VCARD',
        ]);

        return implode("\r\n", $lines)."\r\n";
    }

    protected static function escape(string $value): string
    {
        return str_replace(['\\', "\r\n", "\n", ',', ';'], ['\\\\', '\\n', '\\n', '\\,', '\;'], $value);
    }
}
