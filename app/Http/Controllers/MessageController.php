<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use Knuckles\Scribe\Attributes\QueryParam;

class MessageController extends Controller
{
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number to fetch', example: 1)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'The number of items per page', example: 15)]
    public function index(Chat $chat)
    {
        return $chat->messages()->latest('updated_at')->with('user')->paginate();
    }

    public function store(StoreMessageRequest $request, Chat $chat)
    {
        $chat->updated_at = now();
        $chat->save();
        $message = Message::make($request->validated());
        $message->user()->associate(auth()->user());
        $message->chat()->associate($chat);
        $message->save();

        return $message;
    }

    public function show(Chat $chat, Message $message)
    {
        return $message;
    }

    public function update(UpdateMessageRequest $request, Chat $chat, Message $message)
    {
        $chat->updated_at = now();
        $chat->save();

        return $message->update($request->validated());
    }

    public function destroy(Chat $chat, Message $message)
    {
        $chat->updated_at = now();
        $chat->save();

        return $message->delete();
    }
}
