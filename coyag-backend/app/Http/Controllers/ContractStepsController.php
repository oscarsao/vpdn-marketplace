<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractStep;
use App\Models\Employee;

class ContractStepsController extends Controller {
    public function setup (Request $request) {
        if ( !Employee::where('user_id', auth()->user()->id)->exists() ) return response()->json(['status' => 'error'], 403);

        $response = [];
        $steps = $request->input('steps');

        foreach ($steps as $step) {
            if (isset($step['id'])) {
                $contractStep = ContractStep::find($step['id']);
            } else {
                $contractStep = new ContractStep();
            }

            $contractStep->order = $step['order'] ? $step['order'] : 0;
            $contractStep->contract_id = $step['contract_id'] ? $step['contract_id'] : null;

            $contractStep->name = $step['name'] ? $step['name'] : '';
            $contractStep->description = $step['description'] ? $step['description'] : '';
            
            $contractStep->type = $step['type'] ? $step['type'] : '';
            $contractStep->status = $step['status'] ? $step['status'] : '';
            $contractStep->revision = $step['revision'] ? $step['revision'] : '';
            
            $contractStep->save();
            $response[] = $contractStep;
        }
        return response()->json($response, 200);
    }
    public function store(Request $request) {
        $this->validate($request, [
            'order' => 'required',
            'contract_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'type' => 'required', // Complete, Files, Date(?)
        ]);

        $contractStep = new ContractStep();
        $contractStep->order = $request->input('order');
        $contractStep->contract_id = $request->input('contract_id');
        $contractStep->name = $request->input('name');
        $contractStep->description = $request->input('description');
        $contractStep->type = $request->input('type');
        $contractStep->status = $request->input('status');
        $contractStep->revision = $request->input('revision');
        
        $contractStep->save();
        return response()->json($contractStep, 200);
    }
    public function update(Request $request, $id) {
        $contractStep = ContractStep::find($id);

        if (Employee::where('user_id', auth()->user()->id)->exists()) {
            $contractStep->order = $request->input('order');
            $contractStep->contract_id = $request->input('contract_id');

            $contractStep->name = $request->input('name');
            $contractStep->description = $request->input('description');
            
            $contractStep->type = $request->input('type');
            $contractStep->revision = $request->input('revision');
        }
        $contractStep->status = $request->input('status');
        $contractStep->save();
        return response()->json($contractStep, 200);
    }
    public function destroy($id) {
        if (Employee::where('user_id', auth()->user()->id)->exists()) {
            $contractStep = ContractStep::find($id);
            $contractStep->delete();
            return response()->json(['status' =>  'success'], 200);
        }
        return response()->json(['status' =>  'error'], 403);
    }
}
