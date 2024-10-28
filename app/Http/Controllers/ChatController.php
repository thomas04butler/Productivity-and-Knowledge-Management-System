<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\QueryParam;

class ChatController extends Controller
{
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number to fetch', example: 1)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'The number of items per page', example: 15)]
    #[QueryParam(name: 'search', type: 'string', description: 'Search for chats by name', example: 'l')]
    public function index()
    {
        $query = Auth::user()->chats()->latest('updated_at');

        if (request('search')) {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        }

        $query->with('users');

        return $query->paginate();
    }

    public function store(StoreChatRequest $request)
    {
        return Chat::create($request->validated());
    }

    public function show(Chat $chat)
    {
        return $chat->load('users');
    }

    public function update(UpdateChatRequest $request, Chat $chat)
    {
        return $chat->update($request->validated());
    }

    public function destroy(Chat $chat)
    {
        return $chat->delete();
    }
}
