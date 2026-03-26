<?php
/* WONT USE */

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(isset(Auth::user()->client->id)) {
            $services = Service::where('flag_active', true)->get();
        } else {
            $services = Service::where('flag_active', true)->get();
        }
        return response()->json( $services, 200);
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
            'name'                              =>  'required|max:255|unique:services',
            'slug'                              =>  'required|max:255|unique:services',
            'description'                       =>  'max:255',
            'flag_active'                       =>  'required|boolean',
            'type'                              =>  'required|in:"Plan","AddOn"',
            'roles_slug'                        =>  'max:255',
            'name_payment'                      =>  'required|max:255|unique:services',
            'slug_payment'                      =>  'required|max:255|unique:services',
            'recommended_price'                 =>  'max:255',
            'flag_recurring_payment'            =>  'required|boolean',
            'flag_payment_in_installments'      =>  'required|boolean',
            'financial_analysis_available'      =>  'required|numeric|min:-1|max:1024',
            'flag_mandatory_payment'            =>  'required|boolean'
        ],
        [
            'name.required'                             =>  'El campo name es requerido',
            'name.max'                                  =>  'El campo name no debe pasar de 255 caracteres',
            'name.unique'                               =>  'El valor del campo name ya está siendo utilizado',
            'slug.required'                             =>  'El campo slug es requerido',
            'slug.max'                                  =>  'El campo slug no debe pasar de 255 caracteres',
            'slug.unique'                               =>  'El valor del campo slug ya está siendo utilizado',
            'description.max'                           =>  'El campo description no debe pasar de 255 caracteres',
            'flag_active.required'                      =>  'El campo flag_active es requerido',
            'flag_active.boolean'                       =>  'El campo flag_active debe tener los valores de 1 o 0',
            'type.required'                             =>  'El campo type es requerido',
            'type.in'                                   =>  'El campo type solo puede contener los valores Plan o AddOn',
            'roles_slug.max'                            =>  'El campo roles_slug no debe pasar de 255 caracteres',
            'name_payment.required'                     =>  'El campo name_payment es requerido',
            'name_payment.max'                          =>  'El campo name_payment no debe pasar de 255 caracteres',
            'name_payment.unique'                       =>  'El valor del campo name_payment ya está siendo utilizado',
            'slug_payment.required'                     =>  'El campo slug_payment es requerido',
            'slug_payment.max'                          =>  'El campo slug_payment no debe pasar de 255 caracteres',
            'slug_payment.unique'                       =>  'El valor del campo slug_payment ya está siendo utilizado',
            'recommended_price.max'                     =>  'El campo recommended_price no debe pasar de 255 caracteres',
            'flag_recurring_payment.required'           =>  'El campo flag_recurring_payment es requerido',
            'flag_recurring_payment.boolean'            =>  'El campo flag_recurring_payment debe tener los valores de 1 o 0',
            'flag_payment_in_installments.required'     =>  'El campo flag_payment_in_installments es requerido',
            'flag_payment_in_installments.boolean'      =>  'El campo flag_payment_in_installments debe tener los valores de 1 o 0',
            'financial_analysis_available.required'     =>  'El campo financial_analysis_available es requerido',
            'financial_analysis_available.numeric'      =>  'El campo financial_analysis_available debe ser numérico',
            'financial_analysis_available.min'          =>  'El campo financial_analysis_available debe ser mayor o igual a -1',
            'financial_analysis_available.max'          =>  'El campo financial_analysis_available debe ser menor o igual a 1024',
            'flag_mandatory_payment.required'           =>  'El campo flag_mandatory_payment es requerido',
            'flag_mandatory_payment.boolean'            =>  'El campo flag_mandatory_payment debe tener los valores de 1 o 0'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $service = new Service();

        $service->name = $request->name;

        $service->slug = $request->slug;

        if($request->exists('description'))
            $service->description = $request->description;

        $service->flag_active = $request->flag_active;

        $service->type = $request->type;

        if($request->exists('roles_slug'))
        {
            if(!$this->allRolesExist($request->roles_slug))
                return response()->json(['errors'   =>  'Ha enviado algún Slug de Rol incorrecto, por favor corrija el error e intente nuevamente'], 422);

            $service->roles_slug = $request->roles_slug;
        }

        if($request->exists('name_payment'))
            $service->name_payment = $request->name_payment;

        if($request->exists('slug_payment'))
            $service->slug_payment = $request->slug_payment;

        if($request->exists('recommended_price'))
            $service->recommended_price = $request->recommended_price;

        $service->name_payment = $request->name_payment;

        $service->slug_payment = $request->slug_payment;

        $service->recommended_price = $request->recommended_price;

        $service->flag_recurring_payment = $request->flag_recurring_payment;

        $service->flag_payment_in_installments = $request->flag_payment_in_installments;

        $service->financial_analysis_available = $request->financial_analysis_available;

        $service->flag_mandatory_payment = $request->flag_mandatory_payment;

        if($service->save())
            return response()->json(['status' =>  'success'], 200);

        return response()->json(['errors' =>  'No se pudo crear el Servicio'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show($idService) {

        $service;

        if(isset(Auth::user()->client->id))
        {
            if(Service::where('id', $idService)->where('flag_active', true)->count() == 0)
                return response()->json(['error' => 'El Servicio no existe o no se encuentra activo'], 422);

            $service = Service::select('id as id_service', 'name as name_service', 'description as description_service', 'type as type_service');
        }
        else
        {
            if(Service::where('id', $idService)->count() == 0)
                return response()->json(['error' => 'El Servicio no existe'], 422);

            $service = Service::select('id as id_service', 'name as name_service', 'slug as slug_service', 'description as description_service', 'type as type_service', 'roles_slug as roles_slug_service', 'name_payment as name_payment_service', 'slug_payment as slug_payment_service', 'recommended_price as recommended_price_service', 'flag_recurring_payment as flag_recurring_payment_service', 'flag_payment_in_installments as flag_payment_in_installments_service', 'financial_analysis_available as financial_analysis_available_service', 'flag_mandatory_payment as flag_mandatory_payment_service', 'created_at as created_at_service', 'flag_active as flag_active');
        }

        $service = $service->where('id', $idService)->first();

        return response()->json([
            'status'    =>  'success',
            'service'   =>  $service,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idService)
    {
        $service = Service::find($idService);
        if($service)
        {

            $validator = Validator::make($request->all(),
            [
                'name'                              =>  'max:255|unique:services',
                'slug'                              =>  'max:255|unique:services',
                'description'                       =>  'max:255',
                'flag_active'                       =>  'boolean',
                'type'                              =>  'in:"Plan","AddOn"',
                'roles_slug'                        =>  'max:255',
                'name_payment'                      =>  'max:255|unique:services',
                'slug_payment'                      =>  'max:255|unique:services',
                'recommended_price'                 =>  'max:255',
                'flag_recurring_payment'            =>  'boolean',
                'flag_payment_in_installments'      =>  'boolean',
                'financial_analysis_available'      =>  'numeric|min:-1|max:1024',
                'flag_mandatory_payment'            =>  'boolean'
            ],
            [
                'name.max'                                  =>  'El campo name no debe pasar de 255 caracteres',
                'name.unique'                               =>  'El valor del campo name ya está siendo utilizado',
                'slug.max'                                  =>  'El campo slug no debe pasar de 255 caracteres',
                'slug.unique'                               =>  'El valor del campo slug ya está siendo utilizado',
                'description.max'                           =>  'El campo description no debe pasar de 255 caracteres',
                'flag_active.boolean'                       =>  'El campo flag_active debe tener los valores de 1 o 0',
                'type.in'                                   =>  'El campo type solo puede contener los valores Plan o AddOn',
                'roles_slug.max'                            =>  'El campo roles_slug no debe pasar de 255 caracteres',
                'name_payment.max'                          =>  'El campo name_payment no debe pasar de 255 caracteres',
                'name_payment.unique'                       =>  'El valor del campo name_payment ya está siendo utilizado',
                'slug_payment.max'                          =>  'El campo slug_payment no debe pasar de 255 caracteres',
                'slug_payment.unique'                       =>  'El valor del campo slug_payment ya está siendo utilizado',
                'recommended_price.max'                     =>  'El campo recommended_price no debe pasar de 255 caracteres',
                'flag_recurring_payment.boolean'            =>  'El campo flag_recurring_payment debe tener los valores de 1 o 0',
                'flag_payment_in_installments.boolean'      =>  'El campo flag_payment_in_installments debe tener los valores de 1 o 0',
                'financial_analysis_available.numeric'      =>  'El campo financial_analysis_available debe ser numérico',
                'financial_analysis_available.min'          =>  'El campo financial_analysis_available debe ser mayor o igual a -1',
                'financial_analysis_available.max'          =>  'El campo financial_analysis_available debe ser menor o igual a 1024',
                'flag_mandatory_payment.boolean'            =>  'El campo flag_mandatory_payment debe tener los valores de 1 o 0'
            ]);

            if($validator->fails())
                return response()->json(['errors'   =>  $validator->errors()], 422);

            if($request->exists('name'))
                $service->name = $request->name;

            if($request->exists('slug'))
                $service->slug = $request->slug;

            if($request->exists('description'))
                $service->description = $request->description;

            if($request->exists('flag_active'))
                $service->flag_active = $request->flag_active;

            if($request->exists('type'))
                $service->type = $request->type;

            if($request->exists('roles_slug'))
            {
                if(!$this->allRolesExist($request->roles_slug))
                    return response()->json(['errors'   =>  'Ha enviado algún Slug de Rol incorrecto, por favor corrija el error e intente nuevamente'], 422);

                $service->roles_slug = $request->roles_slug;
            }

            if($request->exists('name_payment'))
                $service->name_payment = $request->name_payment;

            if($request->exists('slug_payment'))
                $service->slug_payment = $request->slug_payment;

            if($request->exists('recommended_price'))
                $service->recommended_price = $request->recommended_price;

            if($request->exists('flag_recurring_payment'))
                $service->flag_recurring_payment = $request->flag_recurring_payment;

            if($request->exists('flag_payment_in_installments'))
                $service->flag_payment_in_installments = $request->flag_payment_in_installments;

            if($request->exists('financial_analysis_available'))
                $service->financial_analysis_available = $request->financial_analysis_available;

            if($request->exists('financial_analysis_available'))
                $service->flag_mandatory_payment = $request->flag_mandatory_payment;

            if($service->save())
                return response()->json(['status' =>  'success'], 200);

            return response()->json(['errors' =>  'No se pudo actualizar el Servicio'], 422);
        }
        else{
            return response()->json(['errors' =>  'No existe el Servicio'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //Antes de culminar este endpoint se debe validar que no haya pagos asociados a este servicio
    }


    private function allRolesExist($allRoles) : bool
    {
        $arrAllRoles = explode(',', $allRoles);

        foreach($arrAllRoles as $rol)
        {
            if(!in_array($rol, ['usuario.premium.mayor', 'usuario.premium.menor', 'usuario.estandar.mayor', 'usuario.estandar.menor', 'usuario.lite', 'cliente.registrado', 'cliente.anual', 'cliente.mensual']))
                return false;
        }

        return true;
    }
}
