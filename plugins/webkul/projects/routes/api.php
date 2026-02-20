<?php

use Illuminate\Support\Facades\Route;
use Webkul\Project\Http\Controllers\API\V1\MilestoneController;
use Webkul\Project\Http\Controllers\API\V1\ProjectController;
use Webkul\Project\Http\Controllers\API\V1\ProjectStageController;
use Webkul\Project\Http\Controllers\API\V1\TagController;
use Webkul\Project\Http\Controllers\API\V1\TaskController;
use Webkul\Project\Http\Controllers\API\V1\TaskStageController;

Route::name('admin.api.v1.projects.')->prefix('admin/api/v1/projects')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('projects', ProjectController::class);
    Route::softDeletableApiResource('tasks', TaskController::class);
    Route::softDeletableApiResource('project-stages', ProjectStageController::class);
    Route::softDeletableApiResource('task-stages', TaskStageController::class);
    Route::apiResource('milestones', MilestoneController::class);
    Route::softDeletableApiResource('tags', TagController::class);
});
