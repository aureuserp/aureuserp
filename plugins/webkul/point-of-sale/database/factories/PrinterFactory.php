<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\PrinterType;
use Webkul\PointOfSale\Models\Printer;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Printer>
 */
class PrinterFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Printer::class;

    public function definition(): array
    {
        return [
            'name'         => fake()->words(2, true),
            'printer_type' => PrinterType::IOT,
            'proxy_ip'     => fake()->localIpv4(),
            'sort'         => fake()->numberBetween(1, 20),
            'creator_id'   => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function epsonEpos(): static
    {
        return $this->state(fn () => ['printer_type' => PrinterType::EPSON_EPOS]);
    }
}
