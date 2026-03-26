<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $families = Family::leftJoin( 'clients', 'clients.id', '=', 'families.client_id')
                            ->leftJoin('family_types', 'family_types.id', '=', 'families.family_type_id')
                            ->leftJoin('countries', 'countries.id', '=', 'families.first_nationality_id');

        if(isset(Auth::user()->client->id))
            $families = $families->where('families.client_id', Auth::user()->client->id);
        else
        {
            if($request->exists('client_id'))
                $families = $families->where('families.client_id', $request->client_id);
        }

        $families = $families->select('families.id as id_family', 'families.names as names_family', 'families.surnames as surnames_family', 'families.email as email_family', 'families.phone_office as phone_office_family', 'families.phone_mobile as phone_mobile_family', 'families.landline as landline_family', 'families.birthdate as birthdate_family', 'clients.id as id_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'family_types.id as id_family_type', 'family_types.name as name_family_type', 'families.first_nationality_id as id_first_nationality', 'countries.name as name_first_nationality', 'families.second_nationality_id as id_second_nationality',)
                                ->get();

        return response()->json(
        [
            'status'    => 'success',
            'families'  => $families
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
            'client_id'                 =>  'required|exists:clients,id',
            'family_type_id'            =>  'required|exists:family_types,id',
            'first_nationality_id'      =>  'exists:countries,id',
            'second_nationality_id'     =>  'exists:countries,id',
            'names'                     =>  'required|max:255',
            'surnames'                  =>  'required|max:255',
            'email'                     =>  'max:255|email|unique:families',
            'phone_office'              =>  'max:20',
            'phone_mobile'              =>  'max:20',
            'landline'                  =>  'max:20',
            'birthdate'                 =>  'date',
        ],
        [
            'client_id.required'                =>  'El ID del Cliente es Requerido',
            'client_id.exists'                  =>  'El ID del CLiente no existe en la BD',
            'family_type_id.required'           =>  'El ID del Tipo de Familiar es Requerido',
            'family_type_id.exists'             =>  'El ID del Tipo de Familiar no existe en la BD',
            'first_nationality_id.exists'       =>  'El ID de la Primera Nacionalidad no existe en la BD',
            'second_nationality_id.exists'      =>  'El ID de la Segunda Nacionalidad no existe en la BD',
            'names.required'                    =>  'Los Nombres son Requeridos',
            'names.max'                         =>  'Los Nombres no pueden exceder los 255 caracteres',
            'surnames.required'                 =>  'Los Apellidos son Requeridos',
            'surnames.max'                      =>  'Los Apellidos no pueden exceder los 255 caracteres',
            'email.max'                         =>  'El Correo Electrónico no puede exceder los 255 caracteres',
            'email.email'                       =>  'El Correo Electrónico tiene un formato inválido',
            'email.unique'                      =>  'El Correo ya está siendo utilizado por otro Familiar',
            'phone_office.max'                  =>  'El Teléfono de la Oficina no puede exceder los 20 caracteres',
            'phone_mobile.max'                  =>  'El Telefono Celular no puede exceder los 20 caracteres',
            'landline.max'                      =>  'El Teléfono fijo no puede exceder los 20 caracteres',
            'birthdate.date'                    =>  'La Fecha de Nacimiento tiene un formato inválido',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $family = new Family();

        $family->client_id = $request->client_id;
        $family->family_type_id = $request->family_type_id;
        $family->names = $request->names;
        $family->surnames = $request->surnames;


        if($request->exists('first_nationality_id'))
            $family->first_nationality_id = $request->first_nationality_id;

        if($request->exists('second_nationality_id'))
            $family->second_nationality_id = $request->second_nationality_id;

        if($request->exists('email'))
            $family->email = $request->email;

        if($request->exists('phone_office'))
            $family->phone_office = $request->phone_office;

        if($request->exists('phone_mobile'))
            $family->phone_mobile = $request->phone_mobile;

        if($request->exists('landline'))
            $family->landline = $request->landline;

        if($request->exists('birthdate'))
            $family->birthdate = $request->birthdate;

        if($family->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo crear al Familiar'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function show($idFamily)
    {
        if(Family::where('id', $idFamily)->count() == 0)
            return response()->json(['errors' => 'El Familiar no existe'], 422);

        $family = Family::leftJoin( 'clients', 'clients.id', '=', 'families.client_id')
                            ->leftJoin('family_types', 'family_types.id', '=', 'families.family_type_id')
                            ->leftJoin('countries', 'countries.id', '=', 'families.first_nationality_id')
                            ->where('families.id', $idFamily);

        if(isset(Auth::user()->client->id))
            $family = $family->where('families.client_id', Auth::user()->client->id);

        $family = $family->select('families.id as id_family', 'families.names as names_family', 'families.surnames as surnames_family', 'families.email as email_family', 'families.phone_office as phone_office_family', 'families.phone_mobile as phone_mobile_family', 'families.landline as landline_family', 'families.birthdate as birthdate_family', 'clients.id as id_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'family_types.id as id_family_type', 'family_types.name as name_family_type', 'families.first_nationality_id as id_first_nationality', 'countries.name as name_first_nationality', 'families.second_nationality_id as id_second_nationality',)
                                ->first();

        return response()->json(
        [
            'status'    => 'success',
            'family'  => $family
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idFamily)
    {
        $family = Family::find($idFamily);

        if(!$family)
            return response()->json(['errors' => 'El Familiar no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'client_id'                 =>  'exists:clients,id',
            'family_type_id'            =>  'exists:family_types,id',
            'first_nationality_id'      =>  'exists:countries,id',
            'second_nationality_id'     =>  'exists:countries,id',
            'names'                     =>  'max:255',
            'surnames'                  =>  'max:255',
            'email'                     =>  'max:255|email',
            'phone_office'              =>  'max:20',
            'phone_mobile'              =>  'max:20',
            'landline'                  =>  'max:20',
            'birthdate'                 =>  'date',
        ],
        [
            'client_id.exists'                  =>  'El ID del CLiente no existe en la BD',
            'family_type_id.exists'             =>  'El ID del Tipo de Familiar no existe en la BD',
            'first_nationality_id.exists'       =>  'El ID de la Primera Nacionalidad no existe en la BD',
            'second_nationality_id.exists'      =>  'El ID de la Segunda Nacionalidad no existe en la BD',
            'names.max'                         =>  'Los Nombres no pueden exceder los 255 caracteres',
            'surnames.max'                      =>  'Los Apellidos no pueden exceder los 255 caracteres',
            'email.max'                         =>  'El Correo Electrónico no puede exceder los 255 caracteres',
            'email.email'                       =>  'El Correo Electrónico tiene un formato inválido',
            'phone_office.max'                  =>  'El Teléfono de la Oficina no puede exceder los 20 caracteres',
            'phone_mobile.max'                  =>  'El Telefono Celular no puede exceder los 20 caracteres',
            'landline.max'                      =>  'El Teléfono fijo no puede exceder los 20 caracteres',
            'birthdate.date'                    =>  'La Fecha de Nacimiento tiene un formato inválido',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('client_id'))
            $family->client_id = $request->client_id;

        if($request->exists('family_type_id'))
            $family->family_type_id = $request->family_type_id;

        if($request->exists('first_nationality_id'))
            $family->first_nationality_id = $request->first_nationality_id;

        if($request->exists('second_nationality_id'))
            $family->second_nationality_id = $request->second_nationality_id;

        if($request->exists('names'))
            $family->names = $request->names;

        if($request->exists('surnames'))
            $family->surnames = $request->surnames;

        if($request->exists('email'))
        {

            if(Family::where('email', $request->email)->where('id', '!=', $family->id)->count() == 1)
                return response()->json(['errors'   =>  'El Correo Electrónico está siendo utilizado'], 422);

            if(Family::where('email', $request->email)->count() == 0  && $family->email != $request->email)
                $family->email = $request->email;

        }

        if($request->exists('phone_office'))
            $family->phone_office = $request->phone_office;

        if($request->exists('phone_mobile'))
            $family->phone_mobile = $request->phone_mobile;

        if($request->exists('landline'))
            $family->landline = $request->landline;

        if($request->exists('birthdate'))
            $family->birthdate = $request->birthdate;

        if($family->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo actualizar al Familiar'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function destroy($idFamily)
    {
        $family = Family::find($idFamily);

        if(!$family)
            return response()->json(['errors' => 'El Familiar no existe'], 422);

        if($family->delete())
            return response()->json(['status' => 'success'], 200);


        return response()->json(['errors' => 'No se pudo eliminar al Familiar'], 422);
    }
}
