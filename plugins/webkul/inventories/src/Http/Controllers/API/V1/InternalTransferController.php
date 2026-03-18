<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Http\Requests\OperationRequest;
use Webkul\Inventory\Http\Resources\V1\InternalTransferResource;
use Webkul\Inventory\Models\InternalTransfer;

#[Group('Inventory API Management')]
#[Subgroup('Internal Transfers', 'Manage inventory internal transfers')]
#[Authenticated]
class InternalTransferController extends OperationController
{
    #[Endpoint('List internal transfers', 'Retrieve a paginated list of internal transfers')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[QueryParam('filter[id]', 'string', 'Filter by internal transfer IDs', required: false, example: '1,2')]
    #[QueryParam('filter[name]', 'string', 'Filter by internal transfer name', required: false, example: 'WH/INT/0001')]
    #[QueryParam('filter[state]', 'string', 'Filter by internal transfer state', required: false, example: 'draft')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'direct')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by partner IDs', required: false, example: '1,2')]
    #[QueryParam('filter[user_id]', 'string', 'Filter by responsible user IDs', required: false, example: '1,2')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false, example: '1,2')]
    #[QueryParam('filter[operation_type_id]', 'string', 'Filter by operation type IDs', required: false, example: '1,2')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', required: false, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', InternalTransfer::class);

        return $this->listOperations();
    }

    #[Endpoint('Create internal transfer', 'Create a new internal transfer')]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, status: 201, additional: ['message' => 'Internal transfer created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(OperationRequest $request)
    {
        Gate::authorize('create', InternalTransfer::class);

        return $this->createOperation($request);
    }

    #[Endpoint('Show internal transfer', 'Retrieve a specific internal transfer by ID')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class)]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $internalTransfer = $this->findOperationForShow($id);

        Gate::authorize('view', $internalTransfer);

        return new InternalTransferResource($internalTransfer);
    }

    #[Endpoint('Update internal transfer', 'Update an existing internal transfer')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(OperationRequest $request, string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        return $this->updateOperationById($request, $id);
    }

    #[Endpoint('Delete internal transfer', 'Delete an internal transfer')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Internal transfer deleted successfully', content: '{"message":"Internal transfer deleted successfully."}')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('delete', $internalTransfer);

        return $this->deleteOperationById($id);
    }

    #[Endpoint('Check internal transfer availability', 'Compute and refresh internal transfer availability')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer availability checked successfully.'])]
    #[Response(status: 422, description: 'Only confirmed or assigned operations can check availability.')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function checkAvailability(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        if ($response = $this->ensureCanCheckAvailability($internalTransfer)) {
            return $response;
        }

        return (new InternalTransferResource($this->checkAvailabilityById($id)))
            ->additional(['message' => 'Internal transfer availability checked successfully.']);
    }

    #[Endpoint('Mark internal transfer as todo', 'Reset internal transfer allocation status')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer set to todo successfully.'])]
    #[Response(status: 422, description: 'Only draft operations can be set to todo.')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function todo(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        if ($response = $this->ensureCanTodo($internalTransfer)) {
            return $response;
        }

        return (new InternalTransferResource($this->todoById($id)))
            ->additional(['message' => 'Internal transfer set to todo successfully.']);
    }

    #[Endpoint('Validate internal transfer', 'Validate internal transfer and complete stock moves')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer validated successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be validated.')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function validateTransfer(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        if ($response = $this->ensureCanValidate($internalTransfer)) {
            return $response;
        }

        return (new InternalTransferResource($this->validateById($id)))
            ->additional(['message' => 'Internal transfer validated successfully.']);
    }

    #[Endpoint('Cancel internal transfer', 'Cancel internal transfer and related moves')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer canceled successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be canceled.')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancelTransfer(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        if ($response = $this->ensureCanCancel($internalTransfer)) {
            return $response;
        }

        return (new InternalTransferResource($this->cancelById($id)))
            ->additional(['message' => 'Internal transfer canceled successfully.']);
    }

    #[Endpoint('Return internal transfer', 'Create a return operation from this internal transfer')]
    #[UrlParam('id', 'integer', 'The internal transfer ID', required: true, example: 1)]
    #[ResponseFromApiResource(InternalTransferResource::class, InternalTransfer::class, additional: ['message' => 'Internal transfer return created successfully.'])]
    #[Response(status: 422, description: 'Only done operations can be returned.')]
    #[Response(status: 404, description: 'Internal transfer not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function returnTransfer(string $id)
    {
        $internalTransfer = $this->findOperationById($id);

        Gate::authorize('update', $internalTransfer);

        if ($response = $this->ensureCanReturn($internalTransfer)) {
            return $response;
        }

        return (new InternalTransferResource($this->returnById($id)))
            ->additional(['message' => 'Internal transfer return created successfully.']);
    }

    protected function modelClass(): string
    {
        return InternalTransfer::class;
    }

    protected function resourceClass(): string
    {
        return InternalTransferResource::class;
    }

    protected function operationType(): OperationTypeEnum
    {
        return OperationTypeEnum::INTERNAL;
    }

    protected function createdMessage(): string
    {
        return 'Internal transfer created successfully.';
    }

    protected function updatedMessage(): string
    {
        return 'Internal transfer updated successfully.';
    }

    protected function deletedMessage(): string
    {
        return 'Internal transfer deleted successfully.';
    }
}
