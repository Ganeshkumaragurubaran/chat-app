<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_image' => 'default-user-image.jpg',

        ]);
   
        return response()->json(['message' => 'Registration successful', 'user' => $user]);
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;

            return response()->json([
                'token' => $token,
                'user'  =>$user,
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
{
    $request->user()->token()->revoke();
    return response()->json([
        'message' => 'Successfully logged out'
    ]);
}
public function me()
{
    return response()->json(auth()->user());
}

public function updateImage(Request $request)
{
    $request->validate([
        'user_image' => 'image|mimes:jpeg,png,jpg,gif', // Adjust the validation rules as needed
    ]);

    // Get the user
    $user = auth()->user();

    // Handle the image upload and update the user's image field
    if ($request->hasFile('user_image')) {
        $image = $request->file('user_image');
        $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('user-images'), $imageName);
        $user->user_image = $imageName;
        $user->save();
    }

    return response()->json([
        'message'=> 'User image updated successfully.'
    ]);
}
}
