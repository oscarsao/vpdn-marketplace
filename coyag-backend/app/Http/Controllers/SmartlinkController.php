<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Smartlink;
use App\Models\User;
use App\Models\Business;

use Illuminate\Support\Facades\DB;

class SmartlinkController extends Controller {
    public function index(Request $request) {
        try {
            $smartlinks = Smartlink::join('employees', 'smartlinks.owner', '=', 'employees.user_id')
                ->select( 'smartlinks.id', 'owner', 'businesses', 'views', 'smartlinks.created_at', DB::raw("CONCAT(employees.name, ' ', employees.surname) as owner_names"))
                ->orderBy('smartlinks.created_at', 'desc')
                ->get()->toArray();
                
            $total = count($smartlinks);
            $response = array('total' => $total, 'status' => 'success');
            $page = $request->exists('page') ? $request->page : 1; // Initial page
            if ($page <= 1) $page = 1;
            $response['page'] = $page;
            $perPage = $request->exists('perPage') ? $request->perPage : 12; // Default lowest per page
            $offset = ($page - 1) * $perPage;
            $finalResult = array_slice($smartlinks, $offset, $perPage);
            
            $response['items']      = count($finalResult);
            $response['businesses'] = $finalResult;
            return response()->json($response, 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }

    /**
     * SMARTLINK REAL FUNCTION
     */
    public function show(Request $request, $id) {
        if ($id === 'top-10') $id = 9999999;
        $smartlink = Smartlink::find($id);
        $smartlink->views += 1;
        $smartlink->save();

        $user = User::where('users.id', $smartlink->owner)->leftJoin('employees', 'users.id', '=', 'employees.user_id')
        ->select('users.id', 'users.email', 'employees.name', 'employees.surname', 'employees.mobile_phone as phone')
        ->first();
        
        $businesses = explode(';', $smartlink->businesses);
        $businesses = Business::whereIn('businesses.id', $businesses)

            ->where('businesses.flag_active', true)
            ->with(array('neighborhood' => function ($query) {
                $query->select('neighborhoods.id', 'neighborhoods.name');
            }))->with(array('district' => function ($query) {
                $query->select('districts.id', 'districts.name');
            }))->with(array('municipality' => function ($query) {
                $query->select('municipalities.id', 'municipalities.name', 'municipalities.province_id')
                    ->with(array('province' => function ($query) {
                        $query->select('provinces.id', 'provinces.name');
                    }));
            }))->with(array('sector' => function ($query) {
                $query->select('sectors.id', 'sectors.name');
            }))->with(array('business_type' => function ($query) {
                $query->select('business_types.id', 'business_types.name');
            }))->with(array('employee' => function ($query) {
                $query->select('employees.id', 'employees.name', 'employees.surname');
            }))->get();

        $smartlinks = $businesses->toArray();
        $total = count($smartlinks);
        $response = array('total' => $total, 'status' => 'success');
        $page = $request->exists('page') ? $request->page : 1; // Initial page
        if ($page <= 1) $page = 1;
        $response['page'] = $page;

        $offset = ($page - 1) * 12;
        $finalResult = array_slice($smartlinks, $offset, 12);
        $response['user']  = $user;
        $response['items'] = count($finalResult);
        $response['businesses'] = $finalResult;
        return response()->json($response, 200);

        if ($smartlink) {
            return response()->json($smartlink, 200);
        } else {
            return response()->json(['error' => 'Smartlink not found'], 404);
        }
    }
    public function store(Request $request) {
        try {
            $businessIds = explode(';', $request->businesses);
            
            $existingSmartlink = Smartlink::where(function ($query) use ($businessIds) {
                foreach ($businessIds as $businessId) {
                    $query->where('businesses', 'LIKE', '%'.$businessId.'%');
                }
            })->first();
            if ($existingSmartlink) {
                return response()->json(['status' => 'success', 'id' => $existingSmartlink->id, 'smartlink' => $existingSmartlink], 200);
            }
            $smartlink = new Smartlink();
            $smartlink->owner = $request->has('owner') ? $request->owner : 1;
            $smartlink->businesses = $request->businesses;
            $smartlink->views = 0;
            $smartlink->save();
            return response()->json(['status' => 'success', 'id' => $smartlink->id, 'smartlink' => $smartlink], 201);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }
    public function update(Request $request, $id) {
        try {
            if ($id === 'top-10') $id = 9999999;
            $smartlink = Smartlink::find($id);
            $smartlink->owner = $request->has('owner') ? $request->owner : 1;
            $smartlink->businesses = $request->businesses;  
            $smartlink->save();
            return response()->json(['status' => 'success', 'id' => $smartlink->id, 'smartlink' => $smartlink], 201);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }
    public function destroy($id) {
        try {
            $smartlink = Smartlink::find($id);
            $smartlink->delete();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }    
}
