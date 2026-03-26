<?php

namespace App\Http\Controllers;

use App\Models\BusinessTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessTimelineController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $businessTimelines = BusinessTimeline::leftJoin('businesses', 'businesses.id', '=', 'business_timelines.business_id')
                                            ->leftJoin('employees', 'employees.id', '=', 'business_timelines.employee_id');

        if($request->exists('business_id'))
            $businessTimelines = $businessTimelines->where('business_timelines.business_id', $request->business_id);

        if($request->exists('employee_id'))
            $businessTimelines = $businessTimelines->where('business_timelines.employee_id', $request->employee_id);

        $retornoBusinessTimelines = $businessTimelines->select('business_timelines.id as id_business_timelines', 'businesses.id as id_business', 'businesses.name as name_business', 'employees.id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', 'business_timelines.module as module_business_timelines', 'business_timelines.type_crud as type_crud_business_timelines', 'business_timelines.business_multimedia as business_multimedia_business_timelines', 'business_timelines.message as message_business_timelines', 'business_timelines.created_at as created_at_business_timelines')
                                        ->get();

        return response()->json([
            'status'    =>  'success',
            'businessTimelines'     =>  $retornoBusinessTimelines->toArray()
        ]);

    }
}
