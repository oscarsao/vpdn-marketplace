<?php

namespace App\Http\Controllers;

use App\Models\UserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userComments = UserComment::leftJoin('user_comment_types', 'user_comment_types.id', '=', 'user_comments.user_comment_type_id')
                                    ->leftJoin('clients', 'clients.id', '=', 'user_comments.client_id')
                                    ->leftJoin('employees', 'employees.id', '=', 'user_comments.employee_id');


        if($request->exists('user_comment_type_id'))
            $userComments = $userComments->where('user_comments.user_comment_type_id', $request->user_comment_type_id);

        if($request->exists('client_id'))
            $userComments = $userComments->where('user_comments.client_id', $request->client_id);

        if($request->exists('employee_id'))
            $userComments = $userComments->where('user_comments.employee_id', $request->employee_id);

        if($request->exists('min_date'))
            $userComments = $userComments->where('user_comments.created_at', '>=', $request->min_date);

        if($request->exists('max_date'))
            $userComments = $userComments->where('user_comments.created_at', '<=', $request->max_date);


        $userComments = $userComments->select('user_comments.id as id_uc', 'user_comments.comment as comment_uc', 'user_comments.created_at as created_at_uc', 'user_comments.updated_at as updated_at_uc', 'user_comment_types.id as id_uct', 'user_comment_types.name as name_uct','clients.id as id_client', DB::raw("CONCAT(clients.names, ' ', clients.surnames) AS full_name_client"), 'employees.id as id_employee', DB::raw("CONCAT(employees.name, ' ', employees.surname) AS full_name_employee"))
                                        ->get();


        return response()->json([
            'status'            =>  'success',
            'userComments'      => $userComments
        ], 200);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'user_comment_type_id'  =>  'required|numeric|exists:user_comment_types,id',
            'client_id'             =>  'required|numeric|exists:clients,id',
            'comment'               =>  'required|max:255'
        ],
        [
            'user_comment_type_id.required' =>  'El ID de Tipo de Comentario de Usuario es requerido',
            'user_comment_type_id.numeric'  =>  'El ID de Tipo de Comentario de Usuario debe ser numérico',
            'user_comment_type_id.exists'   =>  'El ID de Tipo de Comentario de Usuario no existe',
            'client_id.required'            =>  'El ID del Cliente es requerido',
            'client_id.numeric'             =>  'El ID del Cliente deber ser numérico',
            'client_id.exists'              =>  'El ID del Cliente no existe',
            'comment.required'              =>  'El Comentario es requerido',
            'comment.max'                   =>  'El Comentario no puede ser mayor a 255 caracteres'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $userComment = new UserComment();
        $userComment->user_comment_type_id = $request->user_comment_type_id;
        $userComment->client_id = $request->client_id;
        $userComment->employee_id = Auth::user()->employee->id;
        $userComment->comment = $request->comment;

        if($userComment->save())
            return response()->json(['status' =>  'success'], 200);

        return response()->json(['errors' =>  'No se pudo guardar el Comentario de Usuario'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserComment  $userComment
     * @return \Illuminate\Http\Response
     */
    public function show($idUserComment)
    {
        if(UserComment::where('id', $idUserComment)->count() == 0)
            return response()->json(['error' => 'El Comentario de Usuario no existe'], 422);

        $userComment = UserComment::leftJoin('user_comment_types', 'user_comment_types.id', '=', 'user_comments.user_comment_type_id')
                                ->leftJoin('clients', 'clients.id', '=', 'user_comments.client_id')
                                ->leftJoin('employees', 'employees.id', '=', 'user_comments.employee_id')
                                ->where('user_comments.id', $idUserComment)
                                ->select('user_comments.id as id_uc', 'user_comments.comment as comment_uc', 'user_comments.created_at as created_at_uc', 'user_comments.updated_at as updated_at_uc', 'user_comment_types.id as id_uct', 'user_comment_types.name as name_uct','clients.id as id_client', DB::raw("CONCAT(clients.names, ' ', clients.surnames) AS full_name_client"), 'employees.id as id_employee', DB::raw("CONCAT(employees.name, ' ', employees.surname) AS full_name_employee"))
                                ->first();

        return response()->json([
            'status'            =>  'success',
            'userComment'       =>  $userComment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserComment  $userComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idUserComment)
    {
        if(UserComment::where('id', $idUserComment)->count() == 0)
            return response()->json(['error' => 'El Comentario de Usuario no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'user_comment_type_id'  =>  'numeric|exists:user_comment_types,id',
            'client_id'             =>  'numeric|exists:clients,id',
            'comment'               =>  'max:255'
        ],
        [
            'user_comment_type_id.numeric'  =>  'El ID de Tipo de Comentario de Usuario debe ser numérico',
            'user_comment_type_id.exists'   =>  'El ID de Tipo de Comentario de Usuario no existe',
            'client_id.numeric'             =>  'El ID del Cliente deber ser numérico',
            'client_id.exists'              =>  'El ID del Cliente no existe',
            'comment.max'                   =>  'El Comentario no puede ser mayor a 255 caracteres'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $userComment = UserComment::find($idUserComment);

        $aux = false;

        if($request->exists('user_comment_type_id')) {
            $aux = true;
            $userComment->user_comment_type_id = $request->user_comment_type_id;
        }

        if($request->exists('client_id')) {
            $aux = true;
            $userComment->client_id = $request->client_id;
        }

        if($request->exists('comment')) {
            $aux = true;
            $userComment->comment = $request->comment;
        }

        if($aux)
            $userComment->employee_id = Auth::user()->employee->id;

        if($userComment->save())
            return response()->json(['status' =>  'success'], 200);

        return response()->json(['errors' =>  'No se pudo actualizar el Comentario de Usuario'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserComment  $userComment
     * @return \Illuminate\Http\Response
     */
    public function destroy($idUserComment)
    {
        if(UserComment::where('id', $idUserComment)->count() == 0)
            return response()->json(['errors' => 'El Comentario de Usuario no existe'], 422);

        $userComment = UserComment::find($idUserComment);

        if($userComment->delete())
            return response()->json(['status' =>  'success'], 200);

        return response()->json(['errors' =>  'No se pudo borrar el Comentario de Usuario'], 422);
    }
}
