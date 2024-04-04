<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Optimization\AntColonyOptimization; // Make sure to import the AntColonyOptimization class

class OptimizationController extends Controller
{
    public function optimize(Request $request)
    {
        // Validate the request data
        $requestData = $request->validate([
            'distanceMatrix' => 'required|array',
            'garbageSize' => 'required|array',
            'timeMatrix' => 'required|array',
            'truckCapacity' => 'required|numeric',
            'parameters' => 'required|array',
            'numRuns' => 'required|numeric',
            'numIterations' => 'required|numeric',
        ]);

        // Extract necessary data from the request
        $distanceMatrix = $requestData['distanceMatrix'];
        $garbageSize = $requestData['garbageSize'];
        $timeMatrix = $requestData['timeMatrix'];
        $truckCapacity = $requestData['truckCapacity'];
        $parameters = $requestData['parameters'];
        $numRuns = $requestData['numRuns'];
        $numIterations = $requestData['numIterations'];

        // Initialize ACO algorithm
        $aco = new AntColonyOptimization($distanceMatrix, $garbageSize, $timeMatrix, $truckCapacity, $parameters);

        // Run ACO algorithm
        $bestRoute = $aco->optimizeRoutes($numRuns, $numIterations);

        // Return the optimized route
        return response()->json($bestRoute);
    }
}
