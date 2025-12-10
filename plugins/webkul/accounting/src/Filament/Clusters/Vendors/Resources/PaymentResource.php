<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentResource as BasePaymentResource;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Accounting\Models\Payment;

class PaymentResource extends BasePaymentResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Vendors::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/vendors/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/vendors/resources/payment.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        $form = parent::form($schema);

        $components = $form->getComponents();

        $group = $components[1]?->getChildComponents()[0] ?? null;

        if ($group) {
            $fields = $group->getChildComponents();
            $fields[0] = $fields[0]->default(PaymentType::SEND->value);

            $fields[1] = $fields[1]->label(__('accounting::filament/resources/payment.form.sections.fields.vender-bank-account'));

            $fields[2] = Forms\Components\Select::make('partner_id')
                ->label(__('accounting::filament/resources/payment.form.sections.fields.vender'))
                ->relationship(
                    'partner',
                    'name',
                    fn ($query) => $query->where('sub_type', 'supplier')->orderBy('id')
                )
                ->searchable()
                ->preload();

            $group->childComponents($fields);
            $components[1]->childComponents([$group]);
        }

        return $form->components($components);
    }

    public static function infolist(Schema $schema): Schema
    {
        $infolist = parent::infolist($schema);

        $components = $infolist->getComponents();

        $group = $components[0]?->getChildComponents()[0] ?? null;

        if ($group) {
            $fields = $group->getChildComponents();

            $fields[2] = $fields[2]->label(__('accounting::filament/resources/payment.form.sections.fields.vender-bank-account'));
            $fields[3] = $fields[3]->label(__('accounting::filament/resources/payment.form.sections.fields.vender'));

            $group->childComponents($fields);
            $components[0]->childComponents([$group]);
        }

        return $infolist->components($components);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view'   => ViewPayment::route('/{record}'),
            'edit'   => EditPayment::route('/{record}/edit'),
        ];
    }
}
