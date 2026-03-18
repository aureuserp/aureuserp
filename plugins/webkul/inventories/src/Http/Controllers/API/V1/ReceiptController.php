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
use Webkul\Inventory\Http\Resources\V1\ReceiptResource;
use Webkul\Inventory\Models\Receipt;

#[Group('Inventory API Management')]
#[Subgroup('Receipts', 'Manage inventory receipts')]
#[Authenticated]
class ReceiptController extends OperationController
{
    #[Endpoint('List receipts', 'Retrieve a paginated list of receipts')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[QueryParam('filter[id]', 'string', 'Filter by receipt IDs', required: false, example: '1,2')]
    #[QueryParam('filter[name]', 'string', 'Filter by receipt name', required: false, example: 'WH/IN/0001')]
    #[QueryParam('filter[state]', 'string', 'Filter by receipt state', required: false, example: 'draft')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'direct')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by partner IDs', required: false, example: '1,2')]
    #[QueryParam('filter[user_id]', 'string', 'Filter by responsible user IDs', required: false, example: '1,2')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false, example: '1,2')]
    #[QueryParam('filter[operation_type_id]', 'string', 'Filter by operation type IDs', required: false, example: '1,2')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', required: false, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Receipt::class);

        return $this->listOperations();
    }

    #[Endpoint('Create receipt', 'Create a new receipt')]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, status: 201, additional: ['message' => 'Receipt created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(OperationRequest $request)
    {
        Gate::authorize('create', Receipt::class);

        return $this->createOperation($request);
    }

    #[Endpoint('Show receipt', 'Retrieve a specific receipt by ID')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include', required: false, example: 'operationType,moves')]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class)]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $receipt = $this->findOperationForShow($id);

        Gate::authorize('view', $receipt);

        return new ReceiptResource($receipt);
    }

    #[Endpoint('Update receipt', 'Update an existing receipt')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"moves.0.product_id": ["The moves.0.product id field is required."]}}')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(OperationRequest $request, string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        return $this->updateOperationById($request, $id);
    }

    #[Endpoint('Delete receipt', 'Delete a receipt')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Receipt deleted successfully', content: '{"message":"Receipt deleted successfully."}')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('delete', $receipt);

        return $this->deleteOperationById($id);
    }

    #[Endpoint('Check receipt availability', 'Compute and refresh receipt availability')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt availability checked successfully.'])]
    #[Response(status: 422, description: 'Only confirmed or assigned operations can check availability.')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function checkAvailability(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        if ($response = $this->ensureCanCheckAvailability($receipt)) {
            return $response;
        }

        return (new ReceiptResource($this->checkAvailabilityById($id)))
            ->additional(['message' => 'Receipt availability checked successfully.']);
    }

    #[Endpoint('Mark receipt as todo', 'Reset receipt allocation status')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt set to todo successfully.'])]
    #[Response(status: 422, description: 'Only draft operations can be set to todo.')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function todo(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        if ($response = $this->ensureCanTodo($receipt)) {
            return $response;
        }

        return (new ReceiptResource($this->todoById($id)))
            ->additional(['message' => 'Receipt set to todo successfully.']);
    }

    #[Endpoint('Validate receipt', 'Validate receipt and complete stock moves')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt validated successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be validated.')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function validateTransfer(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        if ($response = $this->ensureCanValidate($receipt)) {
            return $response;
        }

        return (new ReceiptResource($this->validateById($id)))
            ->additional(['message' => 'Receipt validated successfully.']);
    }

    #[Endpoint('Cancel receipt', 'Cancel receipt and related moves')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt canceled successfully.'])]
    #[Response(status: 422, description: 'Only non-done and non-canceled operations can be canceled.')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancelTransfer(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        if ($response = $this->ensureCanCancel($receipt)) {
            return $response;
        }

        return (new ReceiptResource($this->cancelById($id)))
            ->additional(['message' => 'Receipt canceled successfully.']);
    }

    #[Endpoint('Return receipt', 'Create a return operation from this receipt')]
    #[UrlParam('id', 'integer', 'The receipt ID', required: true, example: 1)]
    #[ResponseFromApiResource(ReceiptResource::class, Receipt::class, additional: ['message' => 'Receipt return created successfully.'])]
    #[Response(status: 422, description: 'Only done operations can be returned.')]
    #[Response(status: 404, description: 'Receipt not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function returnTransfer(string $id)
    {
        $receipt = $this->findOperationById($id);

        Gate::authorize('update', $receipt);

        if ($response = $this->ensureCanReturn($receipt)) {
            return $response;
        }

        return (new ReceiptResource($this->returnById($id)))
            ->additional(['message' => 'Receipt return created successfully.']);
    }

    protected function modelClass(): string
    {
        return Receipt::class;
    }

    protected function resourceClass(): string
    {
        return ReceiptResource::class;
    }

    protected function operationType(): OperationTypeEnum
    {
        return OperationTypeEnum::INCOMING;
    }

    protected function createdMessage(): string
    {
        return 'Receipt created successfully.';
    }

    protected function updatedMessage(): string
    {
        return 'Receipt updated successfully.';
    }

    protected function deletedMessage(): string
    {
        return 'Receipt deleted successfully.';
    }
}
