<?php

use App\Models\Group;
use App\Events\GroupCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
   Route::post('logout', [AuthController::class,'logout']);
   Route::post('me', [AuthController::class,'me']);
   Route::post('/user/update-image', [AuthController::class, 'updateImage']);
   Route::get('/search',[UserController::class,'search']);
   Route::post('/send-friend-request', [UserController::class, 'sendRequest']);
   Route::get('/friend-requests', [UserController::class, 'getFriendRequests']);
   Route::post('/friend-requests/{requestId}/accept', [UserController::class, 'acceptFriendRequest']);
   Route::get('/get/friend/list', [UserController::class, 'getFriendList']);


   Route::get('/messages', [ChatController::class, 'index']);
   Route::post('/messages', [ChatController::class, 'store']);
   Route::get('/getConversationMessages', [ChatController::class, 'getConversationMessages']);
   Route::get('/conversations/{friendId}/{authId}/messages', [ChatController::class, 'getUserMessages']);
   Route::post('/conversations/{userId1}/{userId2}/messages', [ChatController::class, 'sendMessage']);



   Route::post('/groups/create', [GroupController::class, 'create']);
   Route::get('/get/group/{groupId}', [GroupController::class, 'getGroups']);
   Route::get('/get/user/group', [GroupController::class, 'getGroupsForUser']);
   Route::post('/add/message/{groupId}', [GroupController::class, 'addMessageToGroup']);
   Route::get('/get/messages/{groupId}', [GroupController::class, 'getGroupMessages']);
   Route::post('/group/messages/{groupId}', [GroupController::class, 'storeMessage']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test-broadcast', function () {
   $group = Group::find(18); // Replace with the actual group ID
   broadcast(new GroupCreated($group));
   return 'Event broadcasted!';
});
