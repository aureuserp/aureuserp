<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Crm\Models\MailServer;

class MailServerForm
{
    public static function configure(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/mail-server.form.'.$key);

        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label($label('name'))
                    ->placeholder($label('name-placeholder'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('host')
                    ->label($label('host'))
                    ->placeholder('imap.gmail.com')
                    ->required()
                    ->maxLength(255),
                Select::make('encryption')
                    ->label($label('encryption'))
                    ->options([
                        MailServer::ENCRYPTION_SSL  => $label('encryptions.ssl'),
                        MailServer::ENCRYPTION_TLS  => $label('encryptions.tls'),
                        MailServer::ENCRYPTION_NONE => $label('encryptions.none'),
                    ])
                    ->default(MailServer::ENCRYPTION_SSL)
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('port', $state === MailServer::ENCRYPTION_SSL ? 993 : 143)),
                TextInput::make('port')
                    ->label($label('port'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->default(993)
                    ->required(),
                TextInput::make('folder')
                    ->label($label('folder'))
                    ->default('INBOX')
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label($label('username'))
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('off'),
                TextInput::make('password')
                    ->label($label('password'))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? $label('password-helper') : null)
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->formatStateUsing(fn (): ?string => null)
                    ->autocomplete('new-password'),
                Toggle::make('validate_certificate')
                    ->label($label('validate-certificate'))
                    ->helperText($label('validate-certificate-helper'))
                    ->default(true)
                    ->visible(fn (Get $get): bool => $get('encryption') !== MailServer::ENCRYPTION_NONE),
                Toggle::make('is_active')
                    ->label($label('active'))
                    ->helperText($label('active-helper'))
                    ->default(true),
            ]);
    }
}
