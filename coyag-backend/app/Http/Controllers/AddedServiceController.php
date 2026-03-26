<?php

namespace App\Http\Controllers;

use App\Models\AddedService;
use App\Models\AssignedAdvisor;
use App\Models\Country;
use App\Models\EmailManagement;
use App\Models\File;
use App\Models\Payment;
use App\Models\VisaStep;
use App\Traits\EmailManagementTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AddedServiceController extends Controller
{
    use EmailManagementTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $addedServices = AddedService::leftJoin('clients', 'clients.id', '=', 'added_services.client_id')
                                        ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                                        ->leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                        ->leftJoin('files', 'files.id', '=', 'added_services.file_id')
                                        ->leftJoin('visa_types', 'visa_types.id', '=', 'added_services.visa_type_id');

        if($request->exists('client_id'))
            $addedServices = $addedServices->where('clients.id', $request->client_id);

        if($request->exists('service_id'))
            $addedServices = $addedServices->where('clients.id', $request->client_id);

        if($request->exists('flag_active_plan'))
            $addedServices = $addedServices->where('added_services.flag_active_plan', $request->flag_active_plan);

        if($request->exists('service_type'))
            $addedServices = $addedServices->where('services.type', $request->service_type);

        if($request->exists('visa_type_id'))
            $addedServices = $addedServices->where('added_services.visa_type_id', $request->visa_type_id);


        $addedServices = $addedServices->select('added_services.id as id_added_service', 'added_services.flag_active_plan as flag_active_plan_added_service', 'added_services.plan_deactivated_at as plan_deactivated_at_added_service', 'added_services.created_at as created_at_added_service', 'added_services.flag_payment_completed as flag_payment_completed_added_service', 'added_services.arrival_date as arrival_date_added_service', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'users.email as email_client', 'users.avatar_profile_image as avatar_client', 'services.id as id_service', 'services.name as name_service', 'services.type as type_service', 'files.id as id_file', 'files.original_name as original_name_file', 'visa_types.id as id_visa_type', 'visa_types.name as name_visa_type')
                                        ->orderBy('id_added_service')
                                        ->get();

        if($request->exists('service_type'))
        {
            if($request->service_type == 'Inmigration')
            {
                for($i = 0; $i < count($addedServices); $i++)
                {
                    $assignedAdvisor = AssignedAdvisor::leftJoin('employees', 'employees.id', '=', 'assigned_advisors.employee_id')
                                                        ->leftJoin('users', 'users.id', '=', 'employees.user_id')
                                                        ->where('assigned_advisors.client_id', '=', $addedServices[$i]["id_client"])
                                                        ->select('employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'), 'users.avatar_profile_image as avatar_employee')
                                                        ->first();

                    $visaSteps = VisaStep::leftJoin('added_services', 'added_services.id', '=', 'visa_steps.added_service_id')
                                                        ->where('visa_steps.added_service_id', '=', $addedServices[$i]["id_added_service"])
                                                        ->select('visa_steps.id as id_visa_step', 'visa_steps.number_client as number_client_visa_step', 'visa_steps.number_advisor as number_advisor_visa_step', 'visa_steps.name as name_visa_step', 'visa_steps.client_description as client_description_visa_step', 'visa_steps.advisor_description as advisor_description_visa_step', 'visa_steps.date_completed as date_completed_visa_step', 'visa_steps.status as status_visa_step', 'visa_steps.created_at as created_at_visa_step', 'visa_steps.updated_at as updated_at_visa_step')
                                                        ->orderBy('visa_steps.number_advisor', 'asc')
                                                        ->get();

                    $firstNationality = Country::leftJoin('clients', 'clients.first_nationality_id', '=', 'countries.id')
                                                            ->where('clients.id', $addedServices[$i]["id_client"])
                                                            ->select('countries.id as id_first_nationality', 'countries.name as name_first_nationality')
                                                            ->first();

                    $secondNationality = Country::leftJoin('clients', 'clients.second_nationality_id', '=', 'countries.id')
                                                            ->where('clients.id', $addedServices[$i]["id_client"])
                                                            ->select('countries.id as id_second_nationality', 'countries.name as name_second_nationality')
                                                            ->first();

                    $welcomeEmail = EmailManagement::where('client_id', $addedServices[$i]["id_client"])
                                                            ->where('type', 'welcome')
                                                            ->get();

                    $addedServices[$i]["assignedAdvisor"] = $assignedAdvisor;

                    $addedServices[$i]["firstNationality"] = $firstNationality;

                    $addedServices[$i]["secondNationality"] = $secondNationality;

                    $addedServices[$i]["visaSteps"] = $visaSteps;

                    $addedServices[$i]["welcomeEmail"] = $welcomeEmail;



                }
            }
        }

        if($request->exists('service_type'))
        {
            if($request->service_type == 'Plan')
            {
                for($i = 0; $i < count($addedServices); $i++)
                {
                    $assignedAdvisor = AssignedAdvisor::leftJoin('employees', 'employees.id', '=', 'assigned_advisors.employee_id')
                                                        ->leftJoin('users', 'users.id', '=', 'employees.user_id')
                                                        ->where('assigned_advisors.client_id', '=', $addedServices[$i]["id_client"])
                                                        ->select('employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'), 'users.avatar_profile_image as avatar_employee')
                                                        ->first();
                    $firstNationality = Country::leftJoin('clients', 'clients.first_nationality_id', '=', 'countries.id')
                                                            ->where('clients.id', $addedServices[$i]["id_client"])
                                                            ->select('countries.id as id_first_nationality', 'countries.name as name_first_nationality')
                                                            ->first();

                    $secondNationality = Country::leftJoin('clients', 'clients.second_nationality_id', '=', 'countries.id')
                                                            ->where('clients.id', $addedServices[$i]["id_client"])
                                                            ->select('countries.id as id_second_nationality', 'countries.name as name_second_nationality')
                                                            ->first();

                    $addedServices[$i]["assignedAdvisor"] = $assignedAdvisor;

                    $addedServices[$i]["firstNationality"] = $firstNationality;

                    $addedServices[$i]["secondNationality"] = $secondNationality;

                }
            }
        }

        return response()->json([
            'status' => 'success',
            'addedServices' =>  $addedServices
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AddedService  $addedService
     * @return \Illuminate\Http\Response
     */
    public function show($idAddedService)
    {
        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   =>  'El AddedService NO existe']);

        $addedService = AddedService::leftJoin('clients', 'clients.id', '=', 'added_services.client_id')
                        ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                        ->leftJoin('services', 'services.id', '=', 'added_services.service_id')
                        ->leftJoin('files', 'files.id', '=', 'added_services.file_id')
                        ->leftJoin('visa_types', 'visa_types.id', '=', 'added_services.visa_type_id')
                        ->where('added_services.id', $idAddedService)
                        ->select('added_services.id as id_added_service', 'added_services.flag_active_plan as flag_active_plan_added_service', 'added_services.plan_deactivated_at as plan_deactivated_at_added_service', 'added_services.created_at as created_at_added_service', 'added_services.flag_payment_completed as flag_payment_completed_added_service', 'added_services.arrival_date as arrival_date_added_service', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'users.email as email_client', 'users.avatar_profile_image as avatar_client', 'services.id as id_service', 'services.name as name_service', 'services.type as type_service', 'files.id as id_file', 'files.original_name as original_name_file', 'visa_types.id as id_visa_type', 'visa_types.name as name_visa_type')
                        ->first();


        return response()->json([
            'status' => 'success',
            'addedService' =>  $addedService
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AddedService  $addedService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddedService $addedService)
    {
        /*
         * Por ahora no está habilitado porque debería hacerse de forma automática
        **/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AddedService  $addedService
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddedService $addedService)
    {
        /*
         * Por ahora no está habilitado porque debería hacerse de forma automática
        **/
    }

    public function addPayment(Request $request, $idAddedService)
    {
        //Agrega un pago normal, amortización o recurrente a un servicio

        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   => 'El AddedService no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'payment_id'            =>  'required|numeric|exists:payments,id'
        ],
        [
            'payment_id.required'   =>  'El ID del Pago es requerido',
            'payment_id.numeric'    =>  'El ID del Pago debe ser numérico',
            'payment_id.exists'     =>  'El Pago no existe en el sistema',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $payment = Payment::find($request->payment_id);

        if($payment->client_id != $addedService->client_id)
            return response()->json(['errors'   =>  'El Cliente que realizó el Pago es diferente al cliente de AddedService'], 422);

        $aux = DB::table('added_service_payment')
        ->where('added_service_id', $addedService->id)
        ->where('payment_id', $payment->id)
        ->count();

        if($aux != 0)
            return response()->json(['errors'   =>  'Este pago ya se encuentra registrado para dicho AddedService'], 422);

        $addedService->payments()->attach($payment->id);

        return response()->json(
        [
            'status' => 'success'
        ], 200);

    }

    public function updatePaymentCompleted(Request $request, $idAddedService)
    {
        //Edita el flag flag_payment_completed de la tabla AddedService

        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   => 'El AddedService no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'flag_payment_completed'            =>  'required|boolean'
        ],
        [
            'flag_payment_completed.required'   =>  'El Flag de Pago Completo (flag_payment_completed) es requerido',
            'flag_payment_completed.boolean'    =>  'El  Flag de Pago Completo (flag_payment_completed) debe tener los valores de 1 o 0',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($addedService->service->flag_recurring_payment)
            return response()->json(['errors'   =>  'No se puede marcar como finalizado el pago porque es recurrente'], 422);


        $addedService->flag_payment_completed = $request->flag_payment_completed;

        if($addedService->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo actualizar el Pago Completado'], 422);
    }

    public function addContract(Request $request, $idAddedService)
    {
        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   =>  'El AddedService no existe'], 422);

        if($addedService->file_id != null)
            return response()->json(['errors'   =>  'El AddedService ya posee un Contrato asociado'], 422);

        if(!in_array($addedService->service->type, ['Plan', 'Inmigration']))
            return response()->json(['errors'   =>  'El Servicio asociado no requiere de un contrato'], 422);

        $validator = Validator::make($request->all(),
        [
            'file'                  =>  'required|mimes:jpg,jpeg,png,pdf,xlx,xlxs,doc,docs,csv|max:5120',
        ],
        [
            'files.required'        =>  'El Archivo es requerido',
            'file.mimes'            =>  'El tipo MIME no es soportado',
            'file.max'              =>  'El tamaño máximo del archivo debe ser menor a 5MB',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $auxFile = $request->file;

        $auxPath = "files/contract/$addedService->client_id";
        $path = public_path($auxPath);

        if (!is_dir($path)) {
            Storage::makeDirectory($path);
        }

        $file = new File();

        $file->client_id        = $addedService->client_id ;
        $file->employee_id      = Auth::user()->employee->id;
        $file->original_name    = $auxFile->getClientOriginalName();
        $file->extension        = $auxFile->extension();
        $auxName                = Str::random(12). '.' . $auxFile->extension();
        $file->size             = $auxFile->getSize();
        $file->mime_type        = $auxFile->getMimeType();
        $file->path             = $auxPath;
        $file->full_path        = $auxPath.'/'.$auxName;

        $file->save();


        $addedService->file_id = $file->id;

        $addedService->save();

        $auxFile->move($path, $auxName);

        return response()->json(['status' => 'success'], 200);
    }

    public function removeContract($idAddedService)
    {
        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   =>  'El AddedService no existe'], 422);

        if($addedService->file_id == null)
            return response()->json(['errors'   =>  'El AddedService no posee un Contrato asociado'], 422);

        if(FileStorage::exists(public_path($addedService->file->full_path))){
            FileStorage::delete(public_path($addedService->file->full_path));
        }

        $file = File::find($addedService->file_id);
        $file->delete();

        $addedService->file_id = null;
        $addedService->save();



        return response()->json(['status' => 'success'], 200);
    }

    public function downloadContract($idAddedService)
    {
        $addedService = AddedService::find($idAddedService);

        if(!$addedService)
            return response()->json(['errors'   =>  'El AddedService no existe'], 422);

        if($addedService->file_id == null)
            return response()->json(['errors'   =>  'El AddedService no posee un Contrato asociado'], 422);

        $headers = array(
            "Content-Type:" => $addedService->file->mime_type,
        );

        return response()->download($addedService->file->full_path, $addedService->file->original_name, $headers);
    }
}
