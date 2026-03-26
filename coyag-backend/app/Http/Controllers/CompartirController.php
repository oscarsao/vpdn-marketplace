<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Business;

class CompartirController extends Controller {
    public function compartir($user, $businesses) {
        try {
            $user = User::where('users.id', $user)->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->select('users.id', 'users.email', 'employees.name', 'employees.surname', 'employees.mobile_phone as phone')
                ->first();
            $businesses = explode(',', $businesses);
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

            return response()->json([ 'status' => 'success', 'user' => $user, 'businesses' => $businesses ], 200);
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ]);
        }
    }
}