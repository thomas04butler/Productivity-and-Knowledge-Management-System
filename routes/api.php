<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatUserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserAuthController;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Knuckles\Scribe\Attributes\QueryParam;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);
Route::post('logout', [UserAuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('chats', ChatController::class);
    // get chats that have been updated most recently
    // e.g. they have most recent messages

    Route::apiResource('chats.messages', MessageController::class)->scoped([
        'message' => 'id',
    ]);

    Route::apiResource('chats.users', ChatUserController::class)->scoped([
        'user' => 'id',
    ]);

    Route::get(
        'users',
        #[QueryParam('search', 'string', 'Search query.', example: 'tasks', required: false)]
        #[QueryParam('page', 'integer', 'Page number.', example: 1, required: false)]
        #[QueryParam('perPage', 'integer', 'Items per page.', example: 15, required: false)]
        function () {
            // Return all users
            $users = User::query();

            $users = $users->where('name', 'like', '%'.request('search').'%')
                ->orWhere('email', 'like', '%'.request('search').'%');

            $users = $users->paginate(request('perPage', '15'));

            return $users;

        }
    );

    Route::get(
        'analytics/projects',
        #[QueryParam('with', 'string[]|string', 'Relationships to include. ', example: 'tasks', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'tasks', required: false)]
        #[QueryParam('where', 'string[][]', 'Field to filter by.', example: [['created_at', '>', '2020-04-20']], required: false)]
        #[QueryParam('whereNotNull', 'string[]', 'Range of values that are not null.', example: ['created_at'], required: false)]
        #[QueryParam('whereColumn', 'string[][]', 'Range of values to compare against other columns.', example: [['created_at', 'created_at']], required: false)]
        #[QueryParam('whereBetween', 'string[][]', 'Field to filter between.', example: ['created_at', ['2000-04-21T22:44:15.000000Z', '2040-04-21T22:44:15.000000Z']], required: false)]
        #[QueryParam('orderBy', 'string[]|string', "Field to order by. Defaults to 'id'.", example: 'created_at', required: false)]
        #[QueryParam('order', 'string', "Order direction. Defaults to 'desc'.", example: 'asc', required: false)]
        #[QueryParam('page', 'integer', 'Page number.', example: 1, required: false)]
        #[QueryParam('perPage', 'integer', 'Items per page.', example: 15, required: false)]
        #[QueryParam('groupBy', 'string[]|string', 'Field to group by.', example: 'project_id', required: false)]
        function () {
            // Return analytics for all projects

            $projects = Project::query();

            if (request('where')) {
                $projects->where(request('where'));
            }

            if (request('whereColumn')) {
                $projects->whereColumn(request('whereColumn'));
            }

            if (request('whereBetween')) {
                $projects->whereBetween(...request('whereBetween'));
            }

            if (request('whereNotNull')) {
                $projects->whereNotNull(request('whereNotNull'));
            }

            $projects = $projects->with(request('with', []))
                ->withCount(request('withCount', []))
                ->orderBy(request('orderBy', 'id'), request('order', 'desc'))
                ->paginate(request('perPage', '15'));

            if (request('groupBy')) {
                $projects = $projects->groupBy(request('groupBy'));
            }

            return $projects;
        }
    )->name('analytics.projects.index');

    Route::get(
        'analytics/projects/{project}',
        #[QueryParam('with', 'string[]|string', 'Relationships to include. ', example: 'tasks', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'tasks', required: false)]
        function ($project) {

            return Project::with(request('with', []))
                ->withCount(request('withCount', []))
                ->findOrFail($project);

            // Return analytics for a specific project
        }
    )->name('analytics.projects.show');

    Route::get(
        'analytics/projects/{project}/tasks',
        #[QueryParam('with', 'string[]|string', 'Relationships to include. ', example: 'user', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'user', required: false)]
        #[QueryParam('where', 'string[][]', 'Field to filter by.', example: [['created_at', '>', '2020-04-20']], required: false)]
        #[QueryParam('whereNotNull', 'string[]', 'Range of values that are not null.', example: ['created_at'], required: false)]
        #[QueryParam('whereColumn', 'string[][]', 'Range of values to compare against other columns.', example: [['created_at', 'created_at']], required: false)]
        #[QueryParam('whereBetween', 'string[][]', 'Field to filter between.', example: ['created_at', ['2000-04-21T22:44:15.000000Z', '2040-04-21T22:44:15.000000Z']], required: false)]
        #[QueryParam('orderBy', 'string[]|string', "Field to order by. Defaults to 'id'.", example: 'created_at', required: false)]
        #[QueryParam('order', 'string', "Order direction. Defaults to 'desc'.", example: 'asc', required: false)]
        #[QueryParam('page', 'integer', 'Page number.', example: 1, required: false)]
        #[QueryParam('perPage', 'integer', 'Items per page.', example: 15, required: false)]
        #[QueryParam('groupBy', 'string[]|string', 'Field to group by.', example: 'project_id', required: false)]
        function ($project) {

            $tasks = Project::findOrFail($project)->tasks();

            if (request('where')) {
                $tasks->where(request('where'));
            }

            if (request('whereColumn')) {
                $tasks->whereColumn(request('whereColumn'));
            }

            if (request('whereBetween')) {
                $tasks->whereBetween(...request('whereBetween'));
            }

            if (request('whereNotNull')) {
                $tasks->whereNotNull(request('whereNotNull'));
            }

            $tasks = $tasks->with(request('with', []))
                ->withCount(request('withCount', []))
                ->orderBy(request('orderBy', 'id'), request('order', 'desc'))
                ->paginate(request('perPage', '15'));

            if (request('groupBy')) {
                $tasks = $tasks->groupBy(request('groupBy'));
            }

            return $tasks;

            // Return analytics for a specific project's tasks
        }
    )->name('analytics.projects.tasks.index');

    Route::get(
        'analytics/users',
        #[QueryParam('with', 'string[]|string', 'Relationships to include. ', example: 'tasks', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'tasks', required: false)]
        #[QueryParam('where', 'string[][]', 'Field to filter by.', example: [['created_at', '>', '2024-04-20']], required: false)]
        #[QueryParam('whereNotNull', 'string[]', 'Range of values that are not null.', example: ['created_at'], required: false)]
        #[QueryParam('whereColumn', 'string[][]', 'Range of values to compare against other columns.', example: [['created_at', 'updated_at']], required: false)]
        #[QueryParam('whereBetween', 'string[][]', 'Field to filter between.', example: ['created_at', ['2000-04-21T22:44:15.000000Z', '2040-04-21T22:44:15.000000Z']], required: false)]
        #[QueryParam('orderBy', 'string[]|string', "Field to order by. Defaults to 'id'.", example: 'created_at', required: false)]
        #[QueryParam('order', 'string', "Order direction. Defaults to 'desc'.", example: 'asc', required: false)]
        #[QueryParam('page', 'integer', 'Page number.', example: 1, required: false)]
        #[QueryParam('perPage', 'integer', 'Items per page.', example: 15, required: false)]
        #[QueryParam('groupBy', 'string[]|string', 'Field to group by.', example: 'project_id', required: false)]
        function () {
            // Return analytics for all users
            $users = User::query();

            if (request('where')) {
                $users->where(request('where'));
            }

            if (request('whereColumn')) {
                $users->whereColumn(request('whereColumn'));
            }

            if (request('whereBetween')) {
                $users->whereBetween(...request('whereBetween'));
            }

            if (request('whereNotNull')) {
                $users->whereNotNull(request('whereNotNull'));
            }

            $users = $users->with(request('with', []))
                ->withCount(request('withCount', []))
                ->orderBy(request('orderBy', 'id'), request('order', 'desc'))
                ->paginate(request('perPage', '15'));

            if (request('groupBy')) {
                $users = $users->groupBy(request('groupBy'));
            }

            return $users;
        }
    )->name('analytics.users.index');

    Route::get(
        'analytics/users/{user}',
        #[QueryParam('with', 'string[]|string', 'Relationships to include. ', example: 'tasks', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'tasks', required: false)]
        function ($user) {
            // Return analytics for a specific user
            return User::with(request('with', []))
                ->withCount(request('withCount', []))
                ->findOrFail($user);
        }
    )->name('analytics.users.show');

    Route::get(
        'analytics/tasks',
        #[QueryParam('with', 'string[]|string', 'Relationships to include.', example: 'user', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'user', required: false)]
        #[QueryParam('where', 'string[][]', 'Field to filter by.', example: [['created_at', '>', '2024-04-20']], required: false)]
        #[QueryParam('whereNotNull', 'string[]', 'Range of values that are not null.', example: ['created_at'], required: false)]
        #[QueryParam('whereColumn', 'string[][]', 'Range of values to compare against other columns.', example: [['updated_at', 'updated_at']], required: false)]
        #[QueryParam('whereBetween', 'string[][]', 'Field to filter between.', example: ['created_at', ['2000-04-21T22:44:15.000000Z', '2040-04-21T22:44:15.000000Z']], required: false)]
        #[QueryParam('orderBy', 'string[]|string', "Field to order by. Defaults to 'id'.", example: 'deadline', required: false)]
        #[QueryParam('order', 'string', "Order direction. Defaults to 'desc'.", example: 'asc', required: false)]
        #[QueryParam('page', 'integer', 'Page number.', example: 1, required: false)]
        #[QueryParam('perPage', 'integer', 'Items per page.', example: 15, required: false)]
        #[QueryParam('groupBy', 'string[]|string', 'Field to group by.', example: 'project_id', required: false)]
        function () {

            // Return analytics for all tasks
            $tasks = Task::query();

            if (request('where')) {
                $tasks->where(request('where'));
            }

            if (request('whereBetween')) {
                $tasks->whereBetween(...request('whereBetween'));
            }

            if (request('whereColumn')) {
                $tasks->whereColumn(request('whereColumn'));
            }

            if (request('whereNotNull')) {
                $tasks->whereNotNull(request('whereNotNull'));
            }

            $tasks = $tasks->with(request('with', []))
                ->withCount(request('withCount', []))
                ->orderBy(request('orderBy', 'id'), request('order', 'desc'))
                ->paginate(request('perPage', '15'));

            if (request('groupBy')) {
                $tasks = $tasks->groupBy(request('groupBy'));
            }

            return $tasks;
        }
    )->name('analytics.tasks.index');

    Route::get(
        'analytics/tasks/{task}',
        #[QueryParam('with', 'string[]|string', 'Relationships to include.', example: 'user', required: false)]
        #[QueryParam('withCount', 'string[]|string', 'Relationships to include counts of. ', example: 'user', required: false)]
        function ($task) {
            // Return analytics for a specific task
            return Task::with(request('with', []))
                ->withCount(request('withCount', []))
                ->findOrFail($task);
        }
    )->name('analytics.tasks.show');

});
