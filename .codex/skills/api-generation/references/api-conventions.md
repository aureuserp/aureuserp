# AureusERP API Conventions

Use this guide for any plugin API. Treat Products as a baseline example, not a hard dependency.

## Baseline examples

Primary baseline:
- `plugins/webkul/products/routes/api.php`
- `plugins/webkul/products/src/Http/Controllers/API/V1/ProductController.php`
- `plugins/webkul/products/src/Http/Controllers/API/V1/CategoryController.php`
- `plugins/webkul/products/src/Http/Controllers/API/V1/TagController.php`
- `plugins/webkul/products/src/Http/Requests/ProductRequest.php`
- `plugins/webkul/products/src/Http/Requests/CategoryRequest.php`
- `plugins/webkul/products/src/Http/Resources/V1/ProductResource.php`
- `plugins/webkul/products/src/Http/Resources/V1/CategoryResource.php`

Cross-plugin consistency examples:
- `plugins/webkul/sales/routes/api.php`
- `plugins/webkul/accounts/routes/api.php`
- `plugins/webkul/support/routes/api.php`

Shared router macro:
- `app/Providers/AppServiceProvider.php` (`softDeletableApiResource`)

## 1) Route conventions

Use route group:
- `name`: `admin.api.v1.<plugin>.`
- `prefix`: `admin/api/v1/<plugin>`
- `middleware`: `auth:sanctum`

Use resource registration style:
- `Route::apiResource('resource-name', ResourceController::class);`
- `Route::softDeletableApiResource('resource-name', ResourceController::class);` for soft-deletable models.
- Nested resources when needed: `Route::apiResource('parents.children', ChildController::class);`

`softDeletableApiResource` automatically adds:
- `POST <resource-path>/{id}/restore` -> `restore`
- `DELETE <resource-path>/{id}/force` -> `forceDestroy`

## 2) Controller conventions

Namespace:
- `Webkul\<Plugin>\Http\Controllers\API\V1`

Class attributes:
- `#[Group('<Plugin> API Management')]`
- `#[Subgroup('<ResourcePlural>', 'Manage <resource> ...')]`
- `#[Authenticated]`

Method-level conventions:
- Add `#[Endpoint(...)]` on every action.
- Add `#[QueryParam(...)]` on list/show actions when query options are supported.
- Add `#[UrlParam(...)]` for each route parameter.
- Add `#[ResponseFromApiResource(...)]` on resource responses.
- Add explicit `#[Response(...)]` for common status codes (401/404/422 and delete success).
- Authorize each action via `Gate::authorize(...)`.
- Use `QueryBuilder::for(...)` for list/show query shaping.
- Use `findOrFail` and `withTrashed()->findOrFail` where applicable.

Preferred CRUD behavior:
- `index`: QueryBuilder + `allowedFilters`/`allowedSorts`/`allowedIncludes` + `paginate()` + resource collection.
- `store`: validate, create, eager load needed relations, return resource with `message`, status `201`.
- `show`: QueryBuilder scoped by id (and parent ids for nested resources), then policy check.
- `update`: validate, update, return resource with `message`.
- `destroy`: delete and return JSON message.
- `restore`: restore soft-deleted model and return resource with `message`.
- `forceDestroy`: permanently delete and return JSON message.

## 3) FormRequest conventions

Namespace:
- `Webkul\<Plugin>\Http\Requests`

Rules conventions:
- Keep `authorize()` as `true`; enforce permissions in controller policies.
- For updates (`PUT/PATCH`), convert required fields to `sometimes|required`.
- Use explicit table names in `exists:<table>,id`.

Documentation conventions:
- Implement `bodyParameters()` with concise descriptions and realistic examples.

## 4) Resource conventions

Namespace:
- `Webkul\<Plugin>\Http\Resources\V1`

`toArray()` conventions:
- Return scalar fields first (`id`, domain fields, FK ids, timestamps, `deleted_at` when relevant).
- Cast numeric/boolean fields where needed.
- Use `whenLoaded(...)` for optional relations.
- Use `Resource::collection($this->whenLoaded(...))` for relation collections.

## 5) Policy and soft delete alignment

If a route uses `Route::softDeletableApiResource`:
- Ensure model uses soft deletes.
- Ensure policy includes `restore()` and `forceDelete()`.
- Ensure controller includes `restore()` and `forceDestroy()`.

## 6) Implementation checklist

1. Create/update `routes/api.php` with grouped routes and resource registrations.
2. Create controller(s) in `src/Http/Controllers/API/V1` with Scribe attributes and `Gate::authorize` calls.
3. Create/update `FormRequest` classes in `src/Http/Requests` with update-aware rules and `bodyParameters()`.
4. Create/update `JsonResource` classes in `src/Http/Resources/V1` with consistent output and `whenLoaded` relations.
5. Ensure model relationships match `allowedIncludes` and resource relations.
6. Ensure policies include all actions used by `Gate::authorize`.
7. Ensure restore/force routes and methods exist for soft-deletable resources.
