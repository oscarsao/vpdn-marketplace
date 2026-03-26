<?php

namespace App\Http\Controllers;

use App\Models\UserComment;
use App\Models\UserCommentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserCommentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userCommentTypes = UserCommentType::select('id as id_uct', 'name as name_uct', 'created_at as created_at_uct', 'updated_at as updated_at_uct')
                                            ->get();

        return response()->json(
            [
                'status' => 'success',
                'userCommentTypes' => $userCommentTypes
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
            'name'              =>  'required|max:64|unique:user_comment_types,name'
        ],
        [
            'name.required'     =>  'El Nombre es requerido',
            'name.max'          =>  'El Nombre no debe exceder los 64 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo utilizado'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $userCommentType = new UserCommentType();
        $userCommentType->name = $request->name;

        if($userCommentType->save())
            return response()->json(['status'   =>  'success'], 200);

        return response()->json(['errors' => 'No se guardó el Tipo de Comentario de Usuario'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserCommentType  $userCommentType
     * @return \Illuminate\Http\Response
     */
    public function show($idUserCommentType)
    {
        if(UserCommentType::where('id', $idUserCommentType)->count() == 0)
            return response()->json(['error' => 'El Tipo de Comentario de Usuario no existe'], 422);

        $userCommentType = UserCommentType::select('id as id_uct', 'name as name_uct', 'created_at as created_at_uct', 'updated_at as updated_at_uct')
                                            ->where('id', $idUserCommentType)
                                            ->first();

        return response()->json(
        [
            'status' => 'success',
            'userCommentType' => $userCommentType
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserCommentType  $userCommentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idUserCommentType)
    {
        if(UserCommentType::where('id', $idUserCommentType)->count() == 0)
            return response()->json(['error' => 'El Tipo de Comentario de Usuario no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'name'              =>  'max:64|unique:user_comment_types,name'
        ],
        [
            'name.max'          =>  'El Nombre no debe exceder los 64 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo utilizado'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $userCommentType = UserCommentType::find($idUserCommentType);

        if($request->exists('name'))
            $userCommentType->name = $request->name;

        if($userCommentType->save())
            return response()->json(['status'   =>  'success'], 200);

        return response()->json(['errors' => 'No se actualizó el Tipo de Comentario de Usuario'], 422);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserCommentType  $userCommentType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idUserCommentType)
    {
        if(UserCommentType::where('id', $idUserCommentType)->count() == 0)
            return response()->json(['errors' => 'El Tipo de Comentario de Usuario no existe'], 422);

        if(UserComment::where('user_comment_type_id', $idUserCommentType)->count() > 0)
            return response()->json(['errors' => 'No se puede eliminar el Tipo de Comentario de Usuario porque al menos existe un comentario de usuario con este tipo'], 422);

        $userCommentType = UserCommentType::find($idUserCommentType);

        if($userCommentType->delete())
            return response()->json(['status' => 'success'], 200);


        return response()->json(['errors' => 'No se pudo borrar el Tipo de Comentario de Usuario'], 422);
    }
}
