<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friend;
use App\Events\TestEvent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Events\FriendRequestSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Broadcasters\Pusher\PusherTestEvent;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('name', 'like', "%$query%")->get();
        if($users->count() === 0){
            return response()->json([
                'message' => "User Not Found"
            ], 404); // You can also return a 404 status code to indicate not found
        }
    

        return response()->json(['users' => $users]);
    }
    public function sendRequest(Request $request)
    {
        $senderId = auth()->id();
        $receiverId = $request->input('receiver_id');

        // Check if the request doesn't already exist
        $existingRequest = FriendRequest::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->first();

        if (!$existingRequest) {
            $friendRequest = FriendRequest::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
            ]);
         // Broadcast event
         broadcast(new TestEvent('Hello, Pusher!'));
         broadcast(new FriendRequestSent($friendRequest, auth()->user()))->toOthers();
        }

        return response()->json(['message' => 'Friend request sent successfully']);
    }
    public function acceptFriendRequest(Request $request,$requestId)
    {
        $senderId = auth()->id();
        $receiverId = $requestId;
        Log::info("Received senderId: $senderId");
        Log::info("Received receiverId: $receiverId");
  

    
        // Check if the users are already in a conversation
        $conversation = Conversation::whereHas('users', function ($query) use ($senderId, $receiverId) {
            $query->where('user_id', $senderId)->orWhere('user_id', $receiverId);
        }, '=', 2)->first();
    
        // If no conversation exists, create a new one
        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$senderId, $receiverId]);
        }
    
        FriendRequest::where('sender_id', $receiverId)
        ->where('receiver_id', $senderId)
        ->update(['status' => 'accepted']);    

        Friend::create([
            'user_id' => $senderId,
            'friend_id' => $receiverId,
        ]);
        Friend::create([
            'user_id' => $receiverId,
            'friend_id' => $senderId,
        ]);
        return response()->json(['message' => 'Friend request accepted successfully']);
    }
    public function getFriendRequests()
    {
        try {
            // Get friend requests where the authenticated user is the receiver and the status is 'pending'
            $friendRequests = FriendRequest::with('sender') // Assuming you have a 'sender' relationship in your FriendRequest model
                ->where('receiver_id', auth()->id())
                ->where('status', 'pending')
                ->get();

            // Optionally, you can format the response to include only the necessary data
            $formattedFriendRequests = $friendRequests->map(function ($friendRequest) {
                return [
                    'id' => $friendRequest->id,
                    'sender_name' => $friendRequest->sender->name, // Access sender's name through the relationship
                    'reciever_id' => $friendRequest->sender->id,
                    // Add other necessary fields
                ];
            });

            return response()->json(['friendRequests' => $formattedFriendRequests]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch friend requests.'], 500);
        }
    }
    public function getFriendList()
    {
        $userId = auth()->id();
        // Log the SQL query
        \DB::listen(function($query) {
            \Log::info($query->sql);
            \Log::info($query->bindings);
            \Log::info($query->time);
        });
    
        // Retrieve the user's friends
        $user = User::with('friends.friend')->find($userId);
        // Log the user and friends
        \Log::info($user);
        \Log::info($user->friends);

        // Format the data
        $formattedFriends = $user->friends->map(function ($friendship) {
            return [
                'id' => $friendship->friend->id,
                'name' => $friendship->friend->name,
                'user_image' => $friendship->friend->user_image,
       
            ];
        });
    
        return response()->json(['friends' => $formattedFriends]);
    }
}
