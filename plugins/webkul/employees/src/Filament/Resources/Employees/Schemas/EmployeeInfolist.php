<?php

namespace Webkul\Employee\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;

class EmployeeInfolist
{
    use HasCustomFields;

    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                Section::make()
                    ->columnSpan('full') // makes the section span the full width of the parent grid/container
                    ->schema([
                        Grid::make()
                            ->schema([
                                Group::make([
                                    TextEntry::make('name')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.name'))
                                        ->weight(FontWeight::Bold)
                                        ->placeholder('—')
                                        ->size(TextSize::Large),
                                    TextEntry::make('job_title')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-title')),
                                ])->columnSpan(1),
                                Group::make([
                                    ImageEntry::make('partner.avatar')
                                        ->hiddenLabel()
                                        ->height(140)
                                        ->circular(),
                                ])->columnSpan(1),
                            ]),
                        Grid::make(['default' => 2])
                            ->schema([
                                TextEntry::make('work_email')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-email'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                    ->icon('heroicon-o-envelope')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('department.complete_name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.department')),
                                TextEntry::make('mobile_phone')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-mobile'))
                                    ->placeholder('—')
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('job.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.job-position')),
                                TextEntry::make('work_phone')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.work-phone'))
                                    ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                    ->icon('heroicon-o-phone')
                                    ->iconPosition(IconPosition::Before),
                                TextEntry::make('parent.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.manager')),
                                TextEntry::make('categories.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.employee-tags'))
                                    ->placeholder('—')
                                    ->state(function (Employee $record): array {
                                        return $record->categories->map(fn ($category) => [
                                            'label' => $category->name,
                                            'color' => $category->color ?? '#808080',
                                        ])->toArray();
                                    })
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state['label'])
                                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                    ->listWithLineBreaks(),
                                TextEntry::make('coach.name')
                                    ->placeholder('—')
                                    ->label(__('employees::filament/resources/employee.infolist.sections.entries.coach')),
                            ]),
                    ]),

                Tabs::make()
                    ->tabs([
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.work-information.title'))
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Grid::make(['default' => 3])
                                    ->schema([
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.location'))
                                                ->schema([
                                                    TextEntry::make('companyAddress.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('workLocation.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.work-location'))
                                                        ->icon('heroicon-o-building-office'),
                                                ]),
                                            Fieldset::make('Approvers')
                                                ->schema([
                                                    TextEntry::make('leaveManager.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.time-off'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user-group'),
                                                    TextEntry::make('attendanceManager.name')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.attendance-manager'))
                                                        ->icon('heroicon-o-user-group'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.schedule'))
                                                ->schema([
                                                    TextEntry::make('calendar.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.working-hours'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                    TextEntry::make('time_zone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.timezone'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-clock'),
                                                ]),
                                        ])->columnSpan(2),
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.organization-details'))
                                                ->schema([
                                                    TextEntry::make('company.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.company'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-briefcase'),
                                                    ColorEntry::make('color')
                                                        ->placeholder('—')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.work-information.entries.color')),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.private-information.title'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Grid::make(['default' => 3])
                                    ->schema([
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-contact'))
                                                ->schema([
                                                    TextEntry::make('private_street1')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_street2')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.street-address-line-2'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_city')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.city'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-building-office'),
                                                    TextEntry::make('private_zip')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.post-code'))
                                                        ->icon('heroicon-o-document-text'),
                                                    TextEntry::make('privateCountry.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('privateState.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('private_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                    TextEntry::make('private_email')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-email'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "mailto:{$state}" : '#')
                                                        ->icon('heroicon-o-envelope'),
                                                    TextEntry::make('private_car_plate')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.private-car-plate'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack'),
                                                    TextEntry::make('distance_home_work')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.distance-home-to-work'))
                                                        ->placeholder('—')
                                                        ->suffix('km')
                                                        ->icon('heroicon-o-map'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.emergency-contact'))
                                                ->schema([
                                                    TextEntry::make('emergency_contact')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-name'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user'),
                                                    TextEntry::make('emergency_phone')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.contact-phone'))
                                                        ->placeholder('—')
                                                        ->url(fn (?string $state) => $state ? "tel:{$state}" : '#')
                                                        ->icon('heroicon-o-phone'),
                                                ]),
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit'))
                                                ->schema([
                                                    TextEntry::make('visa_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document-text')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('permit_no')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-rectangle-stack')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-number-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('visa_expire')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.visa-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->visa_expire && now()->diffInDays($record->visa_expire, false) <= 30
                                                            ? 'danger'
                                                            : 'success'
                                                        ),
                                                    TextEntry::make('work_permit_expiration_date')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-expiration-date'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->date('F j, Y')
                                                        ->color(
                                                            fn ($record) => $record->work_permit_expiration_date && now()->diffInDays($record->work_permit_expiration_date, false) <= 30
                                                            ? 'danger'
                                                            : 'success'
                                                        ),
                                                    ImageEntry::make('work_permit')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.work-permit-document'))
                                                        ->columnSpanFull()
                                                        ->placeholder('—')
                                                        ->height(200),
                                                ]),
                                        ])->columnSpan(2),
                                        Group::make([
                                            Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.citizenship'))
                                                ->columns(1)
                                                ->schema([
                                                    TextEntry::make('country.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('state.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.state'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-map'),
                                                    TextEntry::make('identification_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id'))
                                                        ->icon('heroicon-o-document-text')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.identification-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('ssnid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid'))
                                                        ->icon('heroicon-o-document-check')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.ssnid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('sinid')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-document')
                                                        ->copyable()
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.sinid-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('passport_id')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id'))
                                                        ->icon('heroicon-o-identification')
                                                        ->copyable()
                                                        ->placeholder('—')
                                                        ->copyMessage(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.passport-id-copy-message'))
                                                        ->copyMessageDuration(1500),
                                                    TextEntry::make('gender')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.gender'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-user')
                                                        ->badge()
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'male'   => 'info',
                                                            'female' => 'success',
                                                            default  => 'warning',
                                                        }),
                                                    TextEntry::make('birthday')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.date-of-birth'))
                                                        ->icon('heroicon-o-calendar')
                                                        ->placeholder('—')
                                                        ->date('F j, Y'),
                                                    TextEntry::make('countryOfBirth.name')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.country-of-birth'))
                                                        ->placeholder('—')
                                                        ->icon('heroicon-o-globe-alt'),
                                                    TextEntry::make('country.phone_code')
                                                        ->label(__('employees::filament/resources/employee.infolist.tabs.private-information.entries.phone-code'))
                                                        ->icon('heroicon-o-phone')
                                                        ->placeholder('—')
                                                        ->prefix('+'),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Tab::make(__('employees::filament/resources/employee.infolist.tabs.settings.title'))
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.employee-settings'))
                                                    ->schema([
                                                        IconEntry::make('is_active')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.active-employee'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('is_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.flexible-work-arrangement'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('is_fully_flexible')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.fully-flexible-schedule'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        IconEntry::make('work_permit_scheduled_activity')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.work-permit-scheduled-activity'))
                                                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                                                        TextEntry::make('user.name')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.related-user'))
                                                            ->placeholder('—')
                                                            ->icon('heroicon-o-user'),
                                                        TextEntry::make('departureReason.name')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-reason')),
                                                        TextEntry::make('departure_date')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-date'))
                                                            ->icon('heroicon-o-calendar-days'),
                                                        TextEntry::make('departure_description')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.departure-description')),
                                                    ])
                                                    ->columns(2),
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-information'))
                                                    ->schema([
                                                        TextEntry::make('lang')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.primary-language')),
                                                        TextEntry::make('additional_note')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.additional-notes'))
                                                            ->columnSpanFull(),
                                                        TextEntry::make('notes')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.notes')),
                                                    ])
                                                    ->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                        Group::make()
                                            ->schema([
                                                Fieldset::make(__('employees::filament/resources/employee.infolist.tabs.settings.entries.attendance-point-of-sale'))
                                                    ->schema([
                                                        TextEntry::make('barcode')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.badge-id'))
                                                            ->icon('heroicon-o-qr-code'),
                                                        TextEntry::make('pin')
                                                            ->placeholder('—')
                                                            ->label(__('employees::filament/resources/employee.infolist.tabs.settings.entries.pin')),
                                                    ])
                                                    ->columns(1),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),

                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpan('full'),
            ]);
    }
}
