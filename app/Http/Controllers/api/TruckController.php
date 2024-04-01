<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TruckResource;
use App\Models\Collector;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        return TruckResource::collection(Truck::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'collector_id' => 'required|exists:collectors,id|unique:trucks,collector_id',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_active' => 'required|boolean',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required',
            'truck_capacity' => 'required',
        ]);

        $truck = Truck::create([
            'collector_id' => $request->collector_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => $request->is_active,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'working_days' => $request->working_days,
            'truck_capacity' => $request->truck_capacity,
        ]);

        return new TruckResource($truck);

    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck)
    {
        return new TruckResource($truck);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Truck $truck)
    {
        $request->validate([
            'collector_id' => 'required|exists:collectors,id|unique:trucks,collector_id,' . $truck->id,
            'is_active' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'working_days' => 'required',
            'truck_capacity' => 'required',
        ]);

        $truck->update([
            'collector_id' => $request->collector_id,

            'is_active' => $request->is_active,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'working_days' => $request->working_days,
            'truck_capacity' => $request->truck_capacity,
        ]);

        return new TruckResource($truck);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Truck $truck)
    {

        $truck->delete();
        return response()->json([
            'message' => 'Truck deleted successfully'
        ]);
    }

    // Update truck position
    public function updateTruckPosition(Request $request, Truck $truck)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $truck->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'id' => $truck->id,
            'latitude' => $truck->latitude,
            'longitude' => $truck->longitude,
        ], 200);
    }

    // check if the truck is near the User location
    public function checkTruckLocation(Request $request, Truck $truck)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $user_latitude = $request->latitude;
        $user_longitude = $request->longitude;
        $truck_latitude = $truck->latitude;
        $truck_longitude = $truck->longitude;

        $distance = $this->distance($user_latitude, $user_longitude, $truck_latitude, $truck_longitude);

        if ($distance <= 1) {
            return response()->json([
                'message' => 'Truck is near your location',
                'distance' => $distance
            ], 200);
        } else {
            return response()->json([
                'message' => 'Truck is not near your location',
                'distance' => $distance
            ], 200);
        }
    }

    // Calculate distance between two points in kilometers unit
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
