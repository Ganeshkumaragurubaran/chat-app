<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        // Fetch all messages
        $messages = Message::with('user')->get();
        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'content' => 'required',
        ]);

        // Create a new message
        $message = Message::create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        // Broadcast event (if you are using broadcasting)
        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => 'Message sent successfully']);
    }
    public function getConversationMessages($conversationId)
{
    // Assuming you have a 'messages' relationship in your Conversation model
    $conversation = Conversation::findOrFail($conversationId);
    $messages = $conversation->messages;

    return response()->json(['messages' => $messages]);
}

public function getUserMessages($userId1, $userId2)
{
    // Assuming you have a 'messages' relationship in your User model
    $user1 = User::findOrFail($userId1);
    $user2 = User::findOrFail($userId2);

    // Get the common conversation between the two users
    $conversation = $user1->conversations()->whereHas('users', function ($query) use ($userId2) {
        $query->where('user_id', $userId2);
    })->first();

    if (!$conversation) {
        // Handle the case where no conversation exists between the users
        return response()->json(['messages' => []]);
    }

    // Retrieve messages for the conversation
    $messages = $conversation->messages;

    return response()->json([
        'messages' => $messages,
        'conversation_id' => $conversation->id,


    ]);
}
public function sendMessage(Request $request, $userId1, $userId2)
{
    // Validate input
    $request->validate([
        'content' => 'required|string',
    ]);

    // Find users
    $user1 = User::findOrFail($userId1);
    $user2 = User::findOrFail($userId2);

    // Find or create a conversation between users
    $conversation = $user1->conversations()->whereHas('users', function ($query) use ($userId2) {
        $query->where('user_id', $userId2);
    })->first();

    if (!$conversation) {
        $conversation = $user1->conversations()->create();
        $conversation->users()->attach([$userId2]);
    }

    // Create and save the message
    $message = new Message([
        'content' => $request->input('content'),
        'user_id' => auth()->id(), // Assuming the authenticated user is the sender
    ]);

    $conversation->messages()->save($message);

    // Broadcast the MessageSent event with the message data
    broadcast(new MessageSent($message, $conversation->id))->toOthers();
    Log::info("conversation->id:$conversation->id");


    return response()->json([
        'message' => 'Message sent successfully',
        'conversation_id' => $conversation->id,
    ]);


}
}
