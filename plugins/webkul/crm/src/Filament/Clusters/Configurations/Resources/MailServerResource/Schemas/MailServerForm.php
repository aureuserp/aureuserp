<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Crm\Enums\MailServerEncryption;

class MailServerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.name'))
                    ->placeholder(__('crm::filament/clusters/configurations/resources/mail-server.form.name-placeholder'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('host')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.host'))
                    ->placeholder('imap.gmail.com')
                    ->required()
                    ->maxLength(255),
                Select::make('encryption')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.encryption'))
                    ->options(MailServerEncryption::class)
                    ->default(MailServerEncryption::SSL)
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?MailServerEncryption $state) => $set('port', $state === MailServerEncryption::SSL ? 993 : 143)),
                TextInput::make('port')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.port'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->default(993)
                    ->required(),
                TextInput::make('folder')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.folder'))
                    ->default('INBOX')
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.username'))
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('off'),
                TextInput::make('password')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.password'))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? __('crm::filament/clusters/configurations/resources/mail-server.form.password-helper') : null)
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->formatStateUsing(fn (): ?string => null)
                    ->autocomplete('new-password'),
                Toggle::make('validate_certificate')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.validate-certificate'))
                    ->helperText(__('crm::filament/clusters/configurations/resources/mail-server.form.validate-certificate-helper'))
                    ->default(true)
                    ->visible(fn (Get $get): bool => $get('encryption') !== MailServerEncryption::NONE),
                Toggle::make('is_active')
                    ->label(__('crm::filament/clusters/configurations/resources/mail-server.form.active'))
                    ->helperText(__('crm::filament/clusters/configurations/resources/mail-server.form.active-helper'))
                    ->default(true),
            ]);
    }
}
