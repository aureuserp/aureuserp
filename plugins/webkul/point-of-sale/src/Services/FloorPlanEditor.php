<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\TableShape;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Table;

class FloorPlanEditor
{
    public function createFloor(Config $config, string $name): Floor
    {
        return DB::transaction(function () use ($config, $name): Floor {
            $floor = Floor::create(['name' => $name]);

            $config->floors()->attach($floor->id);

            return $floor->refresh();
        });
    }

    public function saveFloor(Floor $floor, array $data): Floor
    {
        return DB::transaction(function () use ($floor, $data): Floor {
            $floor->forceFill([
                'name'             => filled($data['name'] ?? null) ? $data['name'] : $floor->name,
                'background_color' => array_key_exists('background_color', $data)
                    ? ($data['background_color'] ?: null)
                    : $floor->background_color,
            ])->save();

            if (! array_key_exists('tables', $data)) {
                return $floor->refresh();
            }

            $kept = [];

            foreach ($data['tables'] as $row) {
                $table = filled($row['id'] ?? null)
                    ? $floor->tables()->whereKey($row['id'])->first()
                    : null;

                $table ??= new Table(['floor_id' => $floor->id]);

                $table->forceFill([
                    'floor_id'     => $floor->id,
                    'table_number' => (string) $row['table_number'],
                    'shape'        => TableShape::tryFrom($row['shape'] ?? '') ?? TableShape::SQUARE,
                    'position_h'   => max(0, (float) ($row['position_h'] ?? 0)),
                    'position_v'   => max(0, (float) ($row['position_v'] ?? 0)),
                    'width'        => max(20, (float) ($row['width'] ?? 80)),
                    'height'       => max(20, (float) ($row['height'] ?? 80)),
                    'seats'        => max(1, (int) ($row['seats'] ?? 2)),
                    'color'        => filled($row['color'] ?? null) ? $row['color'] : null,
                ])->save();

                $kept[] = $table->id;
            }

            $removed = $floor->tables()->whereNotIn('id', $kept)->get();

            $this->assertTablesFree($removed->pluck('id')->all());

            $removed->each(fn (Table $table) => $table->delete());

            return $floor->refresh();
        });
    }

    public function setBackgroundImage(Floor $floor, UploadedFile $image): Floor
    {
        $previous = $floor->background_image;

        $path = $image->store('point-of-sale/floors', 'public');

        $floor->forceFill(['background_image' => $path])->save();

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return $floor->refresh();
    }

    public function removeBackgroundImage(Floor $floor): Floor
    {
        $previous = $floor->background_image;

        $floor->forceFill(['background_image' => null])->save();

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        return $floor->refresh();
    }

    public function deleteFloor(Floor $floor): void
    {
        DB::transaction(function () use ($floor): void {
            $this->assertTablesFree($floor->tables()->pluck('id')->all());

            $floor->configs()->detach();

            $floor->tables()->each(fn (Table $table) => $table->delete());

            $floor->delete();
        });
    }

    protected function assertTablesFree(array $tableIds): void
    {
        if ($tableIds === []) {
            return;
        }

        $busy = Order::withoutGlobalScopes()
            ->whereIn('table_id', $tableIds)
            ->where('state', OrderState::DRAFT)
            ->with('table')
            ->get()
            ->pluck('table.table_number')
            ->filter()
            ->unique();

        if ($busy->isEmpty()) {
            return;
        }

        throw new PosConfigurationException(
            __('point-of-sale::system.floor-plan.tables-in-use', ['tables' => $busy->implode(', ')])
        );
    }
}
