<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collector;
use App\Models\Business;

use Illuminate\Support\Facades\DB;

class CollectorController extends Controller {

    public function indexCollector(Request $request) {
        
    }
    public function index(Request $request) {
        try {
            
            $collectorsA = Collector::join('employees', 'collectors.owner', '=', 'employees.user_id')
                ->select( 'collectors.id', 'collector_id', 'owner', 'inputs', 'collectors.created_at as date', DB::raw("CONCAT(employees.name, ' ', employees.surname) as owner_names"))
                ->get()->toArray();
            
            $collectorsB = Business::select('job_id', 'collector_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as date'))->where('flag_active', true)->groupBy('collector_id')->get()->toArray();
            
            $collectors = [];

            foreach ($collectorsA as $a) {
                $collector = $a;
                $collector['total'] = 0;
                $collector['date'] = date('Y-m-d H:i:s', strtotime($a['date']));
                foreach ($collectorsB as $b) {
                    if ($a['collector_id'] == $b['collector_id']) {
                        $collector['total'] = $b['total'];
                        $collector['date'] = $b['date'];
                        break;
                    }
                }
                unset($collector['created_at']);
                $collectors[] = $collector;
            }

            foreach ($collectorsB as $b) {
                $collector = $b;
                $collector['id'] = null;
                $collector['owner'] = null;
                $collector['inputs'] = null;
                $collector['owner_names'] = null;
                
                // Check if the collector already exists in $collectorsA
                $existingCollector = array_filter($collectorsA, function ($a) use ($b) {
                    return $a['collector_id'] == $b['collector_id'];
                });
                

                if ( $collector['collector_id'] === null) {
                    $collector['date'] = null;
                }

                if (empty($existingCollector)) {
                    $collectors[] = $collector;
                }
            }

            usort($collectors, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            $total = count($collectors);
            $response = array('total' => $total, 'status' => 'success');
            $page = $request->exists('page') ? $request->page : 1; // Initial page
            if ($page <= 1) $page = 1;
            $response['page'] = $page;
            $perPage = $request->exists('perPage') ? $request->perPage : 12; // Default lowest per page
            $offset = ($page - 1) * $perPage;
            $finalResult = array_slice($collectors, $offset, $perPage);
            
            
            $response['items']      = count($finalResult);
            $response['businesses'] = $finalResult;
            return response()->json($response, 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }
    public function store(Request $request) {
        try {
            $existingCollector = Collector::where('collector_id', 'LIKE', '%' . $request->collector_id . '%')->first();
            if ($existingCollector) {
                return response()->json(['status' => 'success', 'id' => $existingCollector->id, 'collector' => $existingCollector], 200);
            }
            $collector = new Collector();
            $collector->owner = $request->owner;
            $collector->inputs = $request->inputs;
            $collector->job_id = $request->job_id;
            $collector->collector_id = $request->collector_id;
            $collector->save();
            return response()->json(['status' => 'success', 'id' => $collector->id, 'smartlink' => $collector], 201);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }   
}
