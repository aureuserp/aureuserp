---
name: api-generation
description: Generate or update Laravel plugin REST APIs in AureusERP for any module. Use when implementing new API resources or refactoring existing endpoints to match shared project conventions for routes, controllers, requests, resources, policies, Spatie QueryBuilder filtering/includes/sorting, soft-delete restore/force endpoints, and Scribe attributes/documentation.
---

# API Generation

Follow this workflow to generate APIs that match shared AureusERP plugin conventions.

## Workflow

1. Read the conventions reference: `references/api-conventions.md`.
2. Inspect the target plugin module (models, existing policies, existing routes).
3. Implement API files in this order:
- route entries in `routes/api.php`
- controller(s) in `src/Http/Controllers/API/V1`
- form request(s) in `src/Http/Requests`
- resource(s) in `src/Http/Resources/V1`
- policy methods (`restore`, `forceDelete`) when using soft deletes
4. Keep naming and response messages consistent with existing APIs in the target plugin.
5. Validate by checking route registration and running targeted tests if present.
6. Write comments only above the function definition. Do not include any comments inside the function body.
7. For additional context, review the Filament resources within the same plugin to extract the workflow.

## Required conventions

- Use route group pattern: `Route::name('admin.api.v1.<plugin>.')->prefix('admin/api/v1/<plugin>')->middleware(['auth:sanctum'])->group(...)`.
- Use `Route::softDeletableApiResource(...)` for soft-deletable resources; implement `restore()` and `forceDestroy()` in controller.
- Use Scribe PHP attributes on controllers and endpoints (`Group`, `Subgroup`, `Authenticated`, `Endpoint`, `QueryParam`, `UrlParam`, `Response`, `ResponseFromApiResource`).
- Use `Gate::authorize(...)` in each endpoint.
- Use `Spatie\QueryBuilder\QueryBuilder` with explicit `allowedFilters`, `allowedSorts`, and `allowedIncludes`.
- Return API resources for CRUD responses; return JSON `{ "message": "..." }` for delete/force-delete success responses.
- In `FormRequest`, use `sometimes|required` behavior for update (`PUT`/`PATCH`) and include `bodyParameters()` for documentation.
- In `JsonResource`, include scalar IDs/timestamps and conditional relations via `whenLoaded(...)`.

## Reference files

- `references/api-conventions.md`: canonical conventions and implementation checklist.
