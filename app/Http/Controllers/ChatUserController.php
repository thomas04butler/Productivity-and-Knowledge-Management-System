<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatUserRequest;
use App\Http\Requests\UpdateChatUserRequest;
use App\Models\Chat;
use App\Models\User;
use Knuckles\Scribe\Attributes\QueryParam;

class ChatUserController extends Controller
{
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number to fetch', example: 1)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'The number of items per page', example: 15)]
    public function index(Chat $chat)
    {
        return $chat->users()->paginate();
    }

    public function store(StoreChatUserRequest $request, Chat $chat)
    {
        $sync = $chat->users()->syncWithoutDetaching([$request->user_id]);
        $chat->users()->updateExistingPivot($request->user_id, ['is_admin' => $request->admin]);

        return $sync;

    }

    public function show(Chat $chat, User $user)
    {
        return $user;
    }

    public function update(UpdateChatUserRequest $request, Chat $chat, User $user)
    {
        return $chat->users()->updateExistingPivot($user, ['is_admin' => $request->admin]);
    }

    public function destroy(Chat $chat, User $user)
    {
        return $chat->users()->detach($user);
    }
}
