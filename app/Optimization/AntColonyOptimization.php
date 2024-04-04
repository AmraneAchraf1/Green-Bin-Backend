<?php

namespace App\Optimization;

class AntColonyOptimization {
    
    private $pheromoneMatrix;
    private $distanceMatrix;
    private $garbageSize;
    private $timeMatrix;
    private $numContainers;
    private $truckCapacity;
    private $parameters;
    
    public function __construct($distanceMatrix, $garbageSize, $timeMatrix, $truckCapacity, $parameters) {
        $this->pheromoneMatrix = []; // Initialize pheromone matrix
        $this->distanceMatrix = $distanceMatrix;
        $this->garbageSize = $garbageSize;
        $this->timeMatrix = $timeMatrix;
        $this->numContainers = count($distanceMatrix);
        $this->truckCapacity = $truckCapacity;
        $this->parameters = $parameters;
        
        // Initialize pheromone levels
        for ($i = 0; $i < $this->numContainers; $i++) {
            for ($j = 0; $j < $this->numContainers; $j++) {
                $this->pheromoneMatrix[$i][$j] = 1; // Initial pheromone level (can be adjusted)
            }
        }
    }
    
    public function optimizeRoutes($numRuns, $numIterations) {
        $bestRoute = [];
        $bestRouteLength = PHP_INT_MAX;
        
        for ($run = 0; $run < $numRuns; $run++) {
            $localBestRoute = $this->runIteration($numIterations);
            $localBestRouteLength = $this->calculateRouteLength($localBestRoute);
            
            if ($localBestRouteLength < $bestRouteLength) {
                $bestRoute = $localBestRoute;
                $bestRouteLength = $localBestRouteLength;
            }
        }
        
        return $bestRoute;
    }
    
    private function runIteration($numIterations) {
        $bestRoute = [];
        $bestRouteLength = PHP_INT_MAX;
        
        for ($iteration = 0; $iteration < $numIterations; $iteration++) {
            // Initialize ants
            $ants = [];
            for ($i = 0; $i < $this->numContainers; $i++) {
                $ants[] = $this->initializeAnt($i);
            }
            
            // Move ants
            foreach ($ants as $ant) {
                $ant->move();
            }
            
            // Update pheromone levels
            $this->updatePheromones($ants);
            
            // Update best route if necessary
            foreach ($ants as $ant) {
                if ($ant->getRouteLength() < $bestRouteLength) {
                    $bestRoute = $ant->getRoute();
                    $bestRouteLength = $ant->getRouteLength();
                }
            }
        }
        
        return $bestRoute;
    }
    
    private function initializeAnt($startContainer) {
        return new Ant($startContainer, $this->distanceMatrix, $this->garbageSize, $this->timeMatrix, $this->pheromoneMatrix, $this->truckCapacity, $this->parameters);
    }
    
    private function updatePheromones($ants) {
        // Evaporation
        for ($i = 0; $i < $this->numContainers; $i++) {
            for ($j = 0; $j < $this->numContainers; $j++) {
                $this->pheromoneMatrix[$i][$j] *= (1 - $this->parameters[4]);
            }
        }
        
        // Deposit pheromones
        foreach ($ants as $ant) {
            $route = $ant->getRoute();
            $routeLength = $ant->getRouteLength();
            $pheromoneDeposit = 1 / $routeLength; // Adjust as needed
            
            for ($i = 0; $i < count($route) - 1; $i++) {
                $from = $route[$i];
                $to = $route[$i + 1];
                $this->pheromoneMatrix[$from][$to] += $pheromoneDeposit;
                $this->pheromoneMatrix[$to][$from] += $pheromoneDeposit;
            }
        }
    }

    private function calculateRouteLength($route) {
        $length = 0;
        for ($i = 0; $i < count($route) - 1; $i++) {
            $from = $route[$i];
            $to = $route[$i + 1];
            $length += $this->distanceMatrix[$from][$to];
        }
        return $length;
    }
}

class Ant {
    
    private $currentContainer;
    private $route;
    private $routeLength;
    private $visited;
    private $distanceMatrix;
    private $garbageSize;
    private $timeMatrix;
    private $pheromoneMatrix;
    private $truckCapacity;
    private $parameters;
    
    public function __construct($startContainer, $distanceMatrix, $garbageSize, $timeMatrix, $pheromoneMatrix, $truckCapacity, $parameters) {
        $this->currentContainer = $startContainer;
        $this->route = [$startContainer];
        $this->routeLength = 0;
        $this->visited = [$startContainer];
        $this->distanceMatrix = $distanceMatrix;
        $this->garbageSize = $garbageSize;
        $this->timeMatrix = $timeMatrix;
        $this->pheromoneMatrix = $pheromoneMatrix;
        $this->truckCapacity = $truckCapacity;
        $this->parameters = $parameters;
    }
    
    public function move() {
        while (count($this->visited) < count($this->distanceMatrix)) {
            $nextContainer = $this->selectNextContainer();
            $this->route[] = $nextContainer;
            $this->visited[] = $nextContainer;
            $this->routeLength += $this->distanceMatrix[$this->currentContainer][$nextContainer];
            $this->currentContainer = $nextContainer;
        }
        
        // Return to the starting container
        $this->route[] = $this->route[0];
        $this->routeLength += $this->distanceMatrix[$this->currentContainer][$this->route[0]];
    }
    
    private function selectNextContainer() {
        $probabilities = [];
        $totalProbability = 0;
        $currentContainer = end($this->route);
        
        // Calculate probabilities
        for ($i = 0; $i < count($this->distanceMatrix); $i++) {
            if (!in_array($i, $this->visited)) {
                $pheromone = $this->pheromoneMatrix[$currentContainer][$i];
                $distance = $this->distanceMatrix[$currentContainer][$i];
                $garbageSize = $this->garbageSize[$i];
                $time = $this->timeMatrix[$currentContainer][$i];
                
                $probability = pow($pheromone, $this->parameters[0]) * pow(1 / $distance, $this->parameters[1]) * pow($garbageSize, $this->parameters[2]) * pow($time, $this->parameters[3]);
                $probabilities[$i] = $probability;
                $totalProbability += $probability;
            }
        }
        
        // Normalize probabilities
        foreach ($probabilities as $key => $value) {
            $probabilities[$key] = $value / $totalProbability;
        }
        
        // Select next container probabilistically
        $randomNumber = mt_rand() / mt_getrandmax();
        $cumulativeProbability = 0;
        
        foreach ($probabilities as $container => $probability) {
            $cumulativeProbability += $probability;
            
            if ($randomNumber <= $cumulativeProbability) {
                return $container;
            }
        }
        
        // If no container is selected (should not happen)
        return end($this->visited); // Return the last visited container
    }
    
    public function getRoute() {
        return $this->route;
    }
    
    public function getRouteLength() {
        return $this->routeLength;
    }
}

