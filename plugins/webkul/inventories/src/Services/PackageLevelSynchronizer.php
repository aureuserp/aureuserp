<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\PackageLevel;

class PackageLevelSynchronizer
{
    public function syncFor(?Operation $operation): void
    {
        if (! $operation) {
            return;
        }

        foreach ($operation->moveLines->groupBy('package_id') as $packageId => $lines) {
            if (! $packageId) {
                continue;
            }

            $package = Package::find($packageId);

            if (! $operation->packageFullyMatched($lines, $package)) {
                continue;
            }

            $operations = $lines->pluck('operation')->unique('id');

            $packageLevels = $operations->flatMap->packageLevels
                ->filter(fn (PackageLevel $level) => $level->package_id === $packageId);

            $unpacked = $lines->filter(fn (MoveLine $line) => ! $line->result_package_id
                && ! in_array($line->state, [MoveState::DONE, MoveState::CANCELED]));

            if ($packageLevels->isEmpty()) {
                $this->createPackageLevel($operations, $package, $unpacked);

                continue;
            }

            $this->attachToExistingLevels($operations, $package, $unpacked, $packageLevels, $lines);
        }
    }

    protected function createPackageLevel(Collection $operations, Package $package, Collection $unpacked): void
    {
        if ($operations->count() !== 1) {
            return;
        }

        $operation = $operations->first();

        $packageLevel = PackageLevel::create([
            'operation_id'            => $operation->id,
            'package_id'              => $package->id,
            'location_id'             => $package->location_id,
            'destination_location_id' => $operation->sharedDestinationLocationId($unpacked) ?? $operation->destination_location_id,
            'company_id'              => $operation->company_id,
        ]);

        $unpacked->each->update(['package_level_id' => $packageLevel->id]);

        if ($package->package_use === PackageUse::DISPOSABLE) {
            $unpacked->each->update(['result_package_id' => $package->id]);
        }
    }

    protected function attachToExistingLevels(
        Collection $operations,
        Package $package,
        Collection $unpacked,
        Collection $packageLevels,
        Collection $allLines
    ): void {
        $withLevelOnMove = $unpacked->filter(fn (MoveLine $line) => $line->move?->package_level_id);

        $withoutLevelOnMove = $unpacked->diff($withLevelOnMove);

        if ($package->package_use === PackageUse::DISPOSABLE) {
            $withLevelOnMove->merge($withoutLevelOnMove)->each->update(['result_package_id' => $package->id]);
        }

        $withLevelOnMove->each(fn (MoveLine $line) => $line->update([
            'package_level_id' => $line->move->package_level_id,
        ]));

        $withoutLevelOnMove->each->update(['package_level_id' => $packageLevels->first()->id]);

        $this->realignLevelDestinations($operations->first(), $packageLevels, $allLines);

        $this->promoteLevelsToMoves($unpacked);
    }

    protected function realignLevelDestinations(Operation $operation, Collection $packageLevels, Collection $allLines): void
    {
        $linesByLevel = $allLines->groupBy('package_level_id');

        foreach ($packageLevels as $packageLevel) {
            $destinationId = $operation->sharedDestinationLocationId($linesByLevel->get($packageLevel->id, collect()))
                ?? $operation->destination_location_id;

            if ($packageLevel->destination_location_id !== $destinationId) {
                $packageLevel->update(['destination_location_id' => $destinationId]);
            }
        }
    }

    protected function promoteLevelsToMoves(Collection $unpacked): void
    {
        foreach ($unpacked->pluck('move')->unique('id') as $move) {
            $levelIds = $move->lines->pluck('package_level_id');

            if ($levelIds->every(fn ($id) => (bool) $id) && $levelIds->unique()->count() === 1) {
                $move->update(['package_level_id' => $move->lines->first()->package_level_id]);
            }
        }
    }
}
