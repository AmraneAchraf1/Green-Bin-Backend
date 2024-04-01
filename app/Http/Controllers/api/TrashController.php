<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrashResource;
use App\Models\Trash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return TrashResource::collection(Trash::where('user_id', $user->id)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bin_id' => 'required|exists:bins,id',
            'trash_type' => 'required|string',
            'image' => 'sometimes|image',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = 'trash_'.time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/trash', $image_name);
            $request->image = $image_name;
        }

        $trash = Trash::create([
            'user_id' => $request->user_id,
            'bin_id' => $request->bin_id,
            'trash_type' => $request->trash_type,
            'image' => $request->image ?? null,
        ]);

        return new TrashResource($trash);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trash $trash)
    {
        return new TrashResource($trash);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trash $trash)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bin_id' => 'required|exists:bins,id',
            'trash_type' => 'required|string',
            'image' => 'sometimes|image',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = 'trash_'.time() . '.' . $image->getClientOriginalExtension();
            $new_image = $image->storeAs('public/trash', $image_name);
            $old_image = $trash->image;
            if ($old_image && $new_image) {
                Storage::disk('public')->delete('trash/' . $old_image);
            }
        }

        $image_name = $image_name ?? $trash->image;

        $trash->update([
            'user_id' => $request->user_id,
            'bin_id' => $request->bin_id,
            'trash_type' => $request->trash_type,
            'image' => $image_name,
        ]);

        return new TrashResource($trash);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trash $trash)
    {
        $trash->delete();
        return response()->json([
            'message' => 'Trash deleted successfully'
        ], 200);
    }
}
