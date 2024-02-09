<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\GroupCreated;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use App\Events\GroupMessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        // Validate the request
        $request->validate([
            'groupName' => 'required|string',
            'selectedUsers' => 'required|array',
        ]);
        $user = Auth::user();

        // Create a group
        $group = Group::create([
            'name' => $request->groupName,
        ]);
        $group->users()->attach($user);

        // Attach selected users to the group
        $group->users()->attach($request->selectedUsers);

        // Broadcast the creation of the group to the associated users
        broadcast(new GroupCreated($group));

        return response()->json([
            'message' => 'Group created successfully',
            'group'=>$group,
        ],201);

    }
    public function getGroups($groupId)
    {

        $group = Group::with('users')->find($groupId);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        // If you want to include the authenticated user in the response
        $authenticatedUser = auth()->user();
        $groupUsers = $group->users->merge([$authenticatedUser]);
        // dd($groupUsers);
        return response()->json(['group' => $groupUsers], 200);
        // Get the currently authenticated user
    //     $user = Auth::user();
    
    //      // Retrieve all groups for the user
    //    $userGroups = $user->groups()->get();
    //    Log::info("User Id's in group: $userGroups");

    //     return response()->json([
    //         'groups' => $userGroups,
    //     ]);
    }



    public function storeMessage(Request $request, $groupId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $group = Group::findOrFail($groupId);


        $message = GroupMessage::create([
            'user_id' => auth()->user()->id, // Assuming you are using authentication
            'group_id' => $group->id,
            'content' => $request->input('content'),
        ]);
        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json(['message' => $message], 201);
    }
    public function getGroupMessages($groupId)
    {
        $group = Group::findOrFail($groupId);

        $messages = GroupMessage::where('group_id', $group->id)
            ->with('user') // Assuming you have a 'user' relationship in the Message model
            ->get();

        return response()->json(['messages' => $messages]);
    }



    public function getGroupsForUser()
    {
        // Retrieve the user with their groups
        $user= Auth::user();
        // dd($user->groups);
        $userGroups = $user->groups;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $userGroups = $user->groups;
        return response()->json(['user_groups' => $userGroups], 200);
    }


//     public function getGroupMessages($groupId)
//     {
//         $group = Group::findOrFail($groupId);

//         // Check if the authenticated user is a member of the group
//         $user = auth()->user();
//         if (!$group->users->contains($user)) {
//             return response()->json(['error' => 'User is not a member of the group'], 403);
//         }

//         // Retrieve the conversation associated with the group
//         $conversation = $group->conversations()->first();

//         // Retrieve the messages for the conversation
//         $messages = $conversation->messages()
//             ->with('user') // Load the user relationship for each message
//             ->orderBy('created_at', 'asc')
//             ->get();

//         return response()->json(['messages' => $messages]);
//     }
//     public function addMessageToGroup(Request $request, $groupId)
// {
//     $request->validate([
//         'content' => 'required|string',
//     ]);

//     $user = Auth::user();

//     $group = Group::findOrFail($groupId);

//     // Check if the user is a member of the group
//     if (!$group->users->contains($user)) {
//         return response()->json(['error' => 'User is not a member of the group'], 403);
//     }

//     $conversation = $group->conversations()->firstOrCreate([]);

//     $message = new Message([
//         'content' => $request->input('content'),
//         'user_id' => $user->id,
//     ]);

//     $conversation->messages()->save($message);



//     return response()->json(['message' => 'Message sent successfully', 'group' => $group, 'message' => $message]);
// }

}
