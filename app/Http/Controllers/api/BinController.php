<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BinResource;
use App\Models\Bin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BinResource::collection(Bin::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       // Validate the request...
        $request->validate([
            'collector_id' => 'required|exists:collectors,id',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'city' => 'required',
            'is_active' => 'required|boolean',
            'waste_type' => 'required|in:street,park,residential,industrial',
            'image' => 'sometimes|image',
            'description' => 'required',
            'status' => 'required|numeric|between:0,100',
        ]);

        // check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = 'bin_'.time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/bins', $image_name);
            $request->image = $image_name;
        }


        $bin = Bin::create([
            'collector_id' => $request->collector_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'is_active' => $request->is_active,
            'waste_type' => $request->waste_type,
            'image' => $request->image,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'bin' => new BinResource($bin),
            'message' => 'Bin created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bin $bin)
    {
        return new BinResource($bin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bin $bin)
    {
        // Validate the request...
        $request->validate([
            'collector_id' => 'required',
            'address' => 'required',
            'city' => 'required',
            'is_active' => 'required|in:0,1',
            'waste_type' => 'required|in:street,park,residential,industrial',
            'image' => 'sometimes|image',
            'description' => 'required',
            'status' => 'required|numeric|between:0,100',
        ]);

        // check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = 'bin_'.time() . '.' . $image->getClientOriginalExtension();
            $old_image = $bin->image;
            $new_image = $image->storeAs('public/bins', $image_name);
            if ($old_image && $new_image) {
                Storage::delete('public/bins/' . $old_image);
            }
        }

        $image_name = $image_name ?? $bin->image;

        $bin->update([
            'collector_id' => $request->collector_id,
            'address' => $request->address,
            'city' => $request->city,
            'is_active' => $request->is_active,
            'waste_type' => $request->waste_type,
            'image' => $image_name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'bin' => new BinResource($bin),
            'message' => 'Bin updated successfully'
        ], 200);
    }

    // Update bin position
    public function updateBinPosition(Request $request, Bin $bin)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $bin->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'id' => $bin->id,
            'latitude' => $bin->latitude,
            'longitude' => $bin->longitude,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bin $bin)
    {
        $bin->delete();
        return response()->json([
            'message' => 'Bin deleted successfully'
        ], 200);
    }

    /**
     * Get all bins by collector
     */
    public function getBinsByCollector(Request $request)
    {
        $collector_id = Auth::guard('sanctum')->user()->id;
        return BinResource::collection(Bin::where('collector_id', $collector_id)->get());
    }

   /**
    * Bin Tracking
    */
    public function nearbyBins(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $bins = Bin::all();
        $nearby_bins = [];
        foreach ($bins as $bin) {
            $bin_latitude = $bin->latitude;
            $bin_longitude = $bin->longitude;
            $distance = $this->distance($latitude, $longitude, $bin_latitude, $bin_longitude);
            if ($distance <= 0.35) {
                $nearby_bins[] = $bin;
            }
        }
        return BinResource::collection($nearby_bins);
    }

    /**
     * Calculate distance between two points
     */
    private function distance($lat1, $lon1, $lat2, $lon2): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        // distance in kilometers
        return $dist * 60 * 1.1515 * 1.609344;

    }

}
