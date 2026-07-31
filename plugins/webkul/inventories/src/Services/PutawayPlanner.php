<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Package;

class PutawayPlanner
{
    public function assignLocations(Collection $moveLines): void
    {
        foreach ($moveLines->groupBy('result_package_id') as $packageId => $lines) {
            $package = $packageId ? Package::find($packageId) : null;

            match (true) {
                (bool) $package?->package_type_id => $this->relocateWholePackage($lines, $package),
                (bool) $package                   => $this->relocatePackageLineByLine($lines),
                default                           => $this->relocateLooseLines($lines),
            };
        }
    }

    protected function relocateWholePackage(Collection $lines, Package $package): void
    {
        $target = $lines->first()->move->destinationLocation->resolvePutawayLocation(
            product: null,
            package: $package,
            excludeMoveLineIds: $lines->pluck('id')->all(),
            products: $lines->pluck('product')->filter()->unique('id')->values(),
        );

        $lines->each(function (MoveLine $line) use ($target) {
            $line->update(['destination_location_id' => $target->id]);

            $line->packageLevel?->update(['destination_location_id' => $target->id]);
        });
    }

    protected function relocatePackageLineByLine(Collection $lines): void
    {
        $excluded = $lines->pluck('id')->all();

        $used = collect();

        foreach ($lines as $line) {
            if ($used->count() > 1) {
                break;
            }

            $target = $line->move->destinationLocation->resolvePutawayLocation(
                product: $line->product,
                quantity: $line->qty,
                excludeMoveLineIds: $excluded,
            );

            $line->update(['destination_location_id' => $target->id]);

            $excluded = array_diff($excluded, [$line->id]);

            $used->push($target->id);
        }

        if ($used->unique()->count() > 1) {
            $this->resetToMoveDestination($lines);

            return;
        }

        $lines->each(fn (MoveLine $line) => $line->packageLevel?->update([
            'destination_location_id' => $line->destination_location_id,
        ]));
    }

    protected function relocateLooseLines(Collection $lines): void
    {
        $excluded = $lines->pluck('id')->all();

        foreach ($lines as $line) {
            $target = $line->move->destinationLocation->resolvePutawayLocation(
                product: $line->product,
                quantity: $line->qty,
                packaging: $line->move->productPackaging,
                excludeMoveLineIds: $excluded,
            );

            if ($target->id !== $line->destination_location_id) {
                $line->update(['destination_location_id' => $target->id]);
            }

            $excluded = array_diff($excluded, [$line->id]);
        }
    }

    protected function resetToMoveDestination(Collection $lines): void
    {
        $lines->groupBy('move_id')->each(function (Collection $grouped, $moveId) {
            $move = Move::find($moveId);

            $grouped->each->update(['destination_location_id' => $move->destination_location_id]);
        });
    }
}
