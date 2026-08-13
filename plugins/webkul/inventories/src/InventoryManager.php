<?php

namespace Webkul\Inventory;

use BackedEnum;
use Illuminate\Support\Collection;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Services\MoveCanceller;
use Webkul\Inventory\Services\MoveCompleter;
use Webkul\Inventory\Services\MoveConfirmer;
use Webkul\Inventory\Services\MoveReserver;
use Webkul\Inventory\Services\MoveStateResolver;
use Webkul\Inventory\Services\ProcurementRunner;
use Webkul\Inventory\Services\RuleResolver;
use Webkul\Inventory\Services\TransferWorkflow;

class InventoryManager
{
    public function __construct(
        protected TransferWorkflow $transfers,
        protected MoveConfirmer $confirmation,
        protected MoveReserver $reservation,
        protected MoveCompleter $completion,
        protected MoveCanceller $cancellation,
        protected ProcurementRunner $procurements,
        protected RuleResolver $rules,
        protected MoveStateResolver $states,
    ) {}

    public function confirmTransfer(Operation $operation): Operation
    {
        return $this->transfers->confirm($operation);
    }

    public function reserveTransfer(Operation $operation): Operation
    {
        return $this->transfers->reserve($operation);
    }

    public function completeTransfer(Operation $operation, bool $cancelBackorder = false): Operation
    {
        return $this->transfers->complete($operation, $cancelBackorder);
    }

    public function cancelTransfer(Operation $operation): Operation
    {
        return $this->transfers->cancel($operation);
    }

    public function createReturn(Operation $operation, array $moveQuantities = []): Operation
    {
        return $this->transfers->createReturn($operation, $moveQuantities);
    }

    public function confirmMoves(Collection $moves, bool $merge = true, ?Collection $mergeInto = null): Collection
    {
        return $this->confirmation->confirm($moves, $merge, $mergeInto);
    }

    public function reserveMoves(Collection $moves, mixed $forceQty = false): void
    {
        $this->reservation->reserve($moves, $forceQty);
    }

    public function releaseMoves(Collection $moves): bool
    {
        return $this->reservation->release($moves);
    }

    public function completeMoves(Collection $moves, bool $cancelBackorder = false): Collection
    {
        return $this->completion->complete($moves, $cancelBackorder);
    }

    public function cancelMoves(Collection $moves): bool
    {
        return $this->cancellation->cancel($moves);
    }

    public function runProcurements(Collection $requests): void
    {
        $this->procurements->run($requests);
    }

    public function findRule(Product $product, Location $location, array $options = []): ?Rule
    {
        return $this->rules->findRule($product, $location, $options);
    }

    public function findPushRule(Product $product, Location $destination, array $options = []): ?Rule
    {
        return $this->rules->findPushRule($product, $destination, $options);
    }

    public function matchRule(?Collection $routes, $packaging, ?Product $product, ?Warehouse $warehouse, array $filters): ?Rule
    {
        return $this->rules->matchRule($routes, $packaging, $product, $warehouse, $filters);
    }

    public function relevantMoveState(Collection $moves): BackedEnum
    {
        return $this->states->relevantState($moves);
    }

    public function impactedOperations(Collection $moves): Collection
    {
        return Operation::impactedBy($moves);
    }

    public function splitShortMoves(Collection $moves): Collection
    {
        return app(Services\BackorderCreator::class)->splitShortMoves($moves);
    }

    public function createBackorder(Operation $operation, ?Collection $moves = null): ?Operation
    {
        return app(Services\BackorderCreator::class)->createFor($operation, $moves);
    }

    public function applyPushRules(Collection $moves): Collection
    {
        return app(Services\PushRuleRunner::class)->run($moves);
    }

    public function applyPutawayStrategy(Collection $moveLines): void
    {
        app(Services\PutawayPlanner::class)->assignLocations($moveLines);
    }

    public function mergeMoves(Collection $moves, ?Collection $mergeInto = null): Collection
    {
        return app(Services\MoveMerger::class)->merge($moves, $mergeInto);
    }

    public function syncPackageLevels(?Operation $operation): void
    {
        app(Services\PackageLevelSynchronizer::class)->syncFor($operation);
    }

    public function assignOperations(Collection $moves): void
    {
        app(Services\OperationAssembler::class)->assignMovesToOperations($moves);
    }

    public function assertTransferReady(Operation $operation): void
    {
        app(Services\TransferValidator::class)->assertReadyToComplete($operation);
    }
}
