<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
    // authentification for user using sanctum

    // register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'image' => 'required|image',
        ]);

        // check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Random and image name and time
            $image_name = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/images', $image_name);
            $request->image = $image_name;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image' => $request->image,
        ]);

        $token = $user->createToken('user_token', ['user'])->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 201);

    }

    // login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('user_token', ['user'])->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 200);

    }

    // logout user
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }

    // get user
    public function getUser(Request $request)
    {
        $user = Auth::user();
        return response()->json(new UserResource($user), 200);
    }

    // update user
    public function updateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'image' => 'sometimes|image',
        ]);

        $user = Auth::user();

        // check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Random and image name and time
            $image_name = time() . '_' . $image->getClientOriginalName();
            $new_image= $image->storeAs('public/images', $image_name);
            $old_image = $user->image;
            if  ($old_image && $new_image) {
                Storage::delete('public/images/' . $old_image);
            }
        }

        $image_name = $image_name ?? $user->image;

        $user->update([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'image' => $image_name,
        ]);

        return response()->json(new UserResource($user), 200);
    }


    // update user position
    public function updateUserPosition(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $user = Auth::user();

        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'id' => $user->id,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
        ], 200);
    }


}
