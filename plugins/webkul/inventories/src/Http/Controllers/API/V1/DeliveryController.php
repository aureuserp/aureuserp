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
use Webkul\Inventory\Http\Resources\V1\DeliveryResource;
use Webkul\Inventory\Models\Delivery;

#[Group('Inventory API Management')]
#[Subgroup('Deliveries', 'Manage inventory deliveries')]
#[Authenticated]
class DeliveryController extends OperationController
{
    #[Endpoint('List deliveries', 'Retrieve a paginated list of deliveries')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[QueryParam('filter[id]', 'string', 'Filter by delivery IDs', required: false, example: '1,2')]
    #[QueryParam('filter[name]', 'string', 'Filter by delivery name', required: false, example: 'WH/OUT/0001')]
    #[QueryParam('filter[state]', 'string', 'Filter by delivery state', required: false, example: 'draft')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'direct')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by partner IDs', required: false, example: '1,2')]
    #[QueryParam('filter[user_id]', 'string', 'Filter by responsible user IDs', required: false, example: '1,2')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false, example: '1,2')]
    #[QueryParam('filter[operation_type_id]', 'string', 'Filter by operation type IDs', required: false, example: '1,2')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', required: false, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Delivery::class);

        return $this->listOperations();
    }

    #[Endpoint('Create delivery', 'Create a new delivery')]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, status: 201, additional: ['message' => 'Delivery created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(OperationRequest $request)
    {
        Gate::authorize('create', Delivery::class);

        return $this->createOperation($request);
    }

    #[Endpoint('Show delivery', 'Retrieve a specific delivery by ID')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class)]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $delivery = $this->findOperationForShow($id);

        Gate::authorize('view', $delivery);

        return new DeliveryResource($delivery);
    }

    #[Endpoint('Update delivery', 'Update an existing delivery')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(OperationRequest $request, string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        return $this->updateOperationById($request, $id);
    }

    #[Endpoint('Delete delivery', 'Delete a delivery')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Delivery deleted successfully', content: '{"message":"Delivery deleted successfully."}')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('delete', $delivery);

        return $this->deleteOperationById($id);
    }

    #[Endpoint('Check delivery availability', 'Compute and refresh delivery availability')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery availability checked successfully.'])]
    #[Response(status: 422, description: 'Only confirmed or assigned operations can check availability.')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function checkAvailability(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        if ($response = $this->ensureCanCheckAvailability($delivery)) {
            return $response;
        }

        return (new DeliveryResource($this->checkAvailabilityById($id)))
            ->additional(['message' => 'Delivery availability checked successfully.']);
    }

    #[Endpoint('Mark delivery as todo', 'Reset delivery allocation status')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery set to todo successfully.'])]
    #[Response(status: 422, description: 'Only draft operations can be set to todo.')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function todo(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        if ($response = $this->ensureCanTodo($delivery)) {
            return $response;
        }

        return (new DeliveryResource($this->todoById($id)))
            ->additional(['message' => 'Delivery set to todo successfully.']);
    }

    #[Endpoint('Validate delivery', 'Validate delivery and complete stock moves')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery validated successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be validated.')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function validateTransfer(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        if ($response = $this->ensureCanValidate($delivery)) {
            return $response;
        }

        return (new DeliveryResource($this->validateById($id)))
            ->additional(['message' => 'Delivery validated successfully.']);
    }

    #[Endpoint('Cancel delivery', 'Cancel delivery and related moves')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery canceled successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be canceled.')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancelTransfer(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        if ($response = $this->ensureCanCancel($delivery)) {
            return $response;
        }

        return (new DeliveryResource($this->cancelById($id)))
            ->additional(['message' => 'Delivery canceled successfully.']);
    }

    #[Endpoint('Return delivery', 'Create a return operation from this delivery')]
    #[UrlParam('id', 'integer', 'The delivery ID', required: true, example: 1)]
    #[ResponseFromApiResource(DeliveryResource::class, Delivery::class, additional: ['message' => 'Delivery return created successfully.'])]
    #[Response(status: 422, description: 'Only done operations can be returned.')]
    #[Response(status: 404, description: 'Delivery not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function returnTransfer(string $id)
    {
        $delivery = $this->findOperationById($id);

        Gate::authorize('update', $delivery);

        if ($response = $this->ensureCanReturn($delivery)) {
            return $response;
        }

        return (new DeliveryResource($this->returnById($id)))
            ->additional(['message' => 'Delivery return created successfully.']);
    }

    protected function modelClass(): string
    {
        return Delivery::class;
    }

    protected function resourceClass(): string
    {
        return DeliveryResource::class;
    }

    protected function operationType(): OperationTypeEnum
    {
        return OperationTypeEnum::OUTGOING;
    }

    protected function createdMessage(): string
    {
        return 'Delivery created successfully.';
    }

    protected function updatedMessage(): string
    {
        return 'Delivery updated successfully.';
    }

    protected function deletedMessage(): string
    {
        return 'Delivery deleted successfully.';
    }
}
