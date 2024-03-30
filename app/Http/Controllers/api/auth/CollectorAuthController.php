<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\Collector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CollectorAuthController extends Controller
{
    // authentification for collector using sanctum guard collector

    // register collector
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:collectors',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'role' => 'required|string',
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

        $collector = Collector::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'role' => $request->role,
            'image' => $request->image,
        ]);

        $token = $collector->createToken('collector_token', ['collector'])->plainTextToken;

        return response()->json([
            'collector' => $collector,
            'token' => $token
        ], 201);

    }

    // login collector
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('collector')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $collector = Collector::where('email', $request->email)->firstOrFail();

        $token = $collector->createToken('collector_token', ['collector'])->plainTextToken;

        return response()->json([
            'collector' => $collector,
            'token' => $token
        ], 200);
    }

    // logout collector
    public function logout(Request $request)
    {
        $collector = Auth::guard('sanctum')->user();
        $collector->tokens()->delete();
        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }

    // get collector
    public function getCollector(Request $request)
    {
        $collector = Auth::guard('sanctum')->user();
        return response()->json([
            'name' => $collector->name,
            'email' => $collector->email,
            'address' => $collector->address,
            'role' => $collector->role,
            'image' => Storage::disk('public')->url('images/' . $collector->image),
        ], 200);
    }

    // update collector
    public function updateCollector(Request $request)
    {
        $collector = Auth::guard('sanctum')->user();
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'role' => 'required|string',
            'image' => 'sometimes|image',
        ]);
        // check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Random and image name and time
            $image_name = time() . '_' . $image->getClientOriginalName();
            $new_image= $image->storeAs('public/images', $image_name);
            $old_image = $collector->image;
            if  ($old_image && $new_image) {
                Storage::delete('public/images/' . $old_image);
            }
            $request->image = $image_name;
        }

        $collector->update([
            'name' => $request->name,
            'address' => $request->address,
            'role' => $request->role,
            'image' => $request->image,
        ]);

        return response()->json([
            'name' => $collector->name,
            'email' => $collector->email,
            'address' => $collector->address,
            'role' => $collector->role,
            'image' => Storage::disk('public')->url('images/' . $collector->image),
        ], 200);
    }





}
