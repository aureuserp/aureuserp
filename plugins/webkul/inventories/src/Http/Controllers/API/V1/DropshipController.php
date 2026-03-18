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
use Webkul\Inventory\Http\Resources\V1\DropshipResource;
use Webkul\Inventory\Models\Dropship;

#[Group('Inventory API Management')]
#[Subgroup('Dropships', 'Manage inventory dropships')]
#[Authenticated]
class DropshipController extends OperationController
{
    #[Endpoint('List dropships', 'Retrieve a paginated list of dropships')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[QueryParam('filter[id]', 'string', 'Filter by dropship IDs', required: false, example: '1,2')]
    #[QueryParam('filter[name]', 'string', 'Filter by dropship name', required: false, example: 'WH/DS/0001')]
    #[QueryParam('filter[state]', 'string', 'Filter by dropship state', required: false, example: 'draft')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'direct')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by partner IDs', required: false, example: '1,2')]
    #[QueryParam('filter[user_id]', 'string', 'Filter by responsible user IDs', required: false, example: '1,2')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false, example: '1,2')]
    #[QueryParam('filter[operation_type_id]', 'string', 'Filter by operation type IDs', required: false, example: '1,2')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', required: false, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Dropship::class);

        return $this->listOperations();
    }

    #[Endpoint('Create dropship', 'Create a new dropship operation')]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, status: 201, additional: ['message' => 'Dropship created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(OperationRequest $request)
    {
        Gate::authorize('create', Dropship::class);

        return $this->createOperation($request);
    }

    #[Endpoint('Show dropship', 'Retrieve a specific dropship by ID')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class)]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $dropship = $this->findOperationForShow($id);

        Gate::authorize('view', $dropship);

        return new DropshipResource($dropship);
    }

    #[Endpoint('Update dropship', 'Update an existing dropship operation')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(OperationRequest $request, string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        return $this->updateOperationById($request, $id);
    }

    #[Endpoint('Delete dropship', 'Delete a dropship operation')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Dropship deleted successfully', content: '{"message":"Dropship deleted successfully."}')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('delete', $dropship);

        return $this->deleteOperationById($id);
    }

    #[Endpoint('Check dropship availability', 'Compute and refresh dropship availability')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship availability checked successfully.'])]
    #[Response(status: 422, description: 'Only confirmed or assigned operations can check availability.')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function checkAvailability(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        if ($response = $this->ensureCanCheckAvailability($dropship)) {
            return $response;
        }

        return (new DropshipResource($this->checkAvailabilityById($id)))
            ->additional(['message' => 'Dropship availability checked successfully.']);
    }

    #[Endpoint('Mark dropship as todo', 'Reset dropship allocation status')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship set to todo successfully.'])]
    #[Response(status: 422, description: 'Only draft operations can be set to todo.')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function todo(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        if ($response = $this->ensureCanTodo($dropship)) {
            return $response;
        }

        return (new DropshipResource($this->todoById($id)))
            ->additional(['message' => 'Dropship set to todo successfully.']);
    }

    #[Endpoint('Validate dropship', 'Validate dropship and complete stock moves')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship validated successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be validated.')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function validateTransfer(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        if ($response = $this->ensureCanValidate($dropship)) {
            return $response;
        }

        return (new DropshipResource($this->validateById($id)))
            ->additional(['message' => 'Dropship validated successfully.']);
    }

    #[Endpoint('Cancel dropship', 'Cancel dropship and related moves')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship canceled successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be canceled.')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancelTransfer(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        if ($response = $this->ensureCanCancel($dropship)) {
            return $response;
        }

        return (new DropshipResource($this->cancelById($id)))
            ->additional(['message' => 'Dropship canceled successfully.']);
    }

    #[Endpoint('Return dropship', 'Create a return operation from this dropship')]
    #[UrlParam('id', 'integer', 'The dropship ID', required: true, example: 1)]
    #[ResponseFromApiResource(DropshipResource::class, Dropship::class, additional: ['message' => 'Dropship return created successfully.'])]
    #[Response(status: 422, description: 'Only done operations can be returned.')]
    #[Response(status: 404, description: 'Dropship not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function returnTransfer(string $id)
    {
        $dropship = $this->findOperationById($id);

        Gate::authorize('update', $dropship);

        if ($response = $this->ensureCanReturn($dropship)) {
            return $response;
        }

        return (new DropshipResource($this->returnById($id)))
            ->additional(['message' => 'Dropship return created successfully.']);
    }

    protected function modelClass(): string
    {
        return Dropship::class;
    }

    protected function resourceClass(): string
    {
        return DropshipResource::class;
    }

    protected function operationType(): OperationTypeEnum
    {
        return OperationTypeEnum::DROPSHIP;
    }

    protected function createdMessage(): string
    {
        return 'Dropship created successfully.';
    }

    protected function updatedMessage(): string
    {
        return 'Dropship updated successfully.';
    }

    protected function deletedMessage(): string
    {
        return 'Dropship deleted successfully.';
    }
}
