<?php
namespace App\Http\Controllers;

use App\Models\ToolSubscription;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\BrightDataScrapper;
use App\Jobs\RunToolSubcriptions;

class ToolSubscriptionsController extends Controller
{
    public function find (Request $request) {
        $userId = Auth::user()->id;
        $subscriptions = ToolSubscription::where('user_id', '=', $userId)->get();
        return $subscriptions;
    }
    public function save(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'tool'        => 'required|string',
            'email'       => 'required|string',
            'data'        => 'required|array',
            'isAutomatic' => 'nullable|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        if (!Auth::user()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // Extract data from the request
        $userId = Auth::user()->id;
        $tool   = $request->tool;
        $email  = $request->email;
        $data   = $request->data;
        $isAutomatic = $request->input('isAutomatic', false);
        if ($isAutomatic) {
            ToolSubscription::updateOrCreate([
                'user_id' => $userId,
                'tool' => $tool
            ], [
                'user_id' => $userId,
                'tool' => $tool,
                'email' => $email,
                'data' => json_encode($data),
            ]);
        }
        if ($tool === 'ExpedienteNacionalidad') {
            $response = BrightDataScrapper::ExpedienteNacionalidad($data);
            return response($response);
        } else if ($tool === 'ProteccionInternacional') {
            $response = BrightDataScrapper::ProteccionInternacional($data);
        } else {
            return response()->json(['error' => "No tool selected"], 400);
        }
        if (isset($response->error)) {
            return response()->json([ 'error' => $response->result ], 500);
        }
        return response()->json([
            'result' => $response->result,
            'resultType' => $response->resultType,
            'document' => $response->document,
            'resultDate' => date('d-m-Y H:m')
        ], 200);
    }
    public function remove(Request $request) {
        $request->validate([
            'tool'        => 'required|string',
        ]);
        $subscription = ToolSubscription::where([['user_id', '=', Auth::user()->id], ['tool', '=', $request->tool]]);
        $subscription->delete();
        return response()->json(['message' => 'Subscription removed successfully'], 200);
    }
    public function trigger() {
        RunToolSubcriptions::dispatch();
        return ToolSubscription::all();
    }
}