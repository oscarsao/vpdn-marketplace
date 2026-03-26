<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\ContractStep;
use App\Models\Employee;
use App\Models\Client;

class ContractsController extends Controller {
    public function index(Request $request) {
        $contracts = Contract::with('steps')->with('area')->with('client')->with('gestor');

        $employee = Employee::where('user_id', auth()->user()->id)->exists();

        if ($employee) {
            $employee = Employee::where('user_id', auth()->user()->id)->first();
            
            if ($request->exists('templates')) {
                $contracts = $contracts->whereIsNull('gestor_id')->whereIsNull('client_id');
            } 
            if ($request->exists('gestor')) {
                $contracts = $contracts->where('gestor_id', $employee->id);
            }
            if ($request->exists('area')) {
                $contracts = $contracts->where('area_id', $request->area);
            }
        } else {
            $client = Client::where('user_id', auth()->user()->id)->first();
            $contracts = $contracts->where('client_id',  $client->id);
        }

        $total = $contracts->count();
        $page = $request->exists('page') ? (int) $request->page : 1;
        if ($page <= 1) $page = 1;
        $perPage = $request->exists('perPage') ? (int) $request->perPage : 12;
        $offset = ($page - 1) * $perPage;
        $contracts = $contracts->offset($offset)->limit($page * $perPage)->get();

        return response()->json([
            'page' => $page,
            'total' => $total,
            'count' => count($contracts),
            'items' => $contracts,
            'status' => 'success'
        ], 200);
    }

    public function show($id) {
        if (Employee::where('user_id', auth()->user()->id)->exists()) {
            $contract = Contract::with('steps')->find($id);
        } else {
            $client = Client::where('user_id', auth()->user()->id)->first();
            $contract = Contract::with('steps')->where('client_id', $client->id)->find($id);
            if (!$contract) return response()->json(['error' => 'Contract not found'], 404);
        }
        return response()->json($contract, 200);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $contract = new Contract();
        $contract->name = $request->input('name');
        $contract->description = $request->input('description');

        if ($request->has('area_id') && $request->input('area_id') !== 'null' && $request->input('area_id') !== NULL) {
            $contract->area_id = $request->input('area_id');
        }
        if ($request->has('client_id') && $request->input('client_id') !== 'null' && $request->input('client_id') !== NULL) {
            $contract->client_id = $request->input('client_id');
        }
        if ($request->has('gestor_id') && $request->input('gestor_id') !== 'null' && $request->input('gestor_id') !== NULL) {
            $contract->gestor_id = $request->input('gestor_id');
        }
        $contract->status = ($request->input('client_id') === 'null' && $request->input('gestor_id') === 'null') ? 'Template' : "ToDo";
        $contract->save();
        
        $contract = Contract::with('steps')->with('area')->with('client')->with('gestor')->find($contract->id);
        return response()->json($contract, 200);
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required'
        ]);
        $contract = Contract::find($id);
        if ($request->has('name')) {
            $contract->name = $request->input('name');
        }
        if ($request->has('description')) {
            $contract->description = $request->input('description');
        }
        
        if ($request->has('area_id') && $request->input('area_id') !== 'null' && $request->input('area_id') !== NULL) {
            $contract->area_id = $request->input('area_id');
        }
        if ($request->has('client_id') && $request->input('client_id') !== 'null' && $request->input('client_id') !== NULL) {
            $contract->client_id = $request->input('client_id');
        }
        if ($request->has('gestor_id') && $request->input('gestor_id') !== 'null' && $request->input('gestor_id') !== NULL) {
            $contract->gestor_id = $request->input('gestor_id');
        }
        if ($request->has('status')) {
            $contract->status = $request->input('status');
        }
        $contract->save();

        $contract = Contract::with('steps')->with('area')->with('client')->with('gestor')->find($id);
        return response()->json($contract, 200);
    }

    public function destroy($id) {
        $contract = Contract::find($id);
        $contract->delete();
        return response()->json(['message' => 'Contract deleted successfully']);
    }
}
