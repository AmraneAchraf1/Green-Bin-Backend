<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
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
            'password' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'image' => 'sometimes|image',
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
            'user' => $user,
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
            'user' => $user,
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
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'address' => $user->address,
            'city' => $user->city,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'image' => Storage::disk('public')->url('images/' . $user->image),
        ], 200);
    }

    // update user
    public function updateUser(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
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
            $request->image = $image_name;
        }

        $user->update([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'image' => $request->image,
        ]);

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'address' => $user->address,
            'city' => $user->city,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'image' => Storage::disk('public')->url('images/' . $user->image),
        ], 200);
    }




}
