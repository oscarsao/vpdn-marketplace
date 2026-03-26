<?php

use App\Models\AssignedAdvisor;
use App\Models\Client;
use App\Models\Employee;
use App\Models\FinancialAnalysis;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\Payment;
use App\Models\VideoCall;
use App\Models\VideoCallType;
use Illuminate\Support\Facades\Log;

function notificationRegistroCliente(Client $client)
{
    /*
    * Ocurre cuando un usuario se registra como cliente
    */
    $notificationType = NotificationType::where('slug', 'registro.cliente')->first();

    $auxData = array(
        "id_client"     =>  $client->id,
        "email_client"   =>  $client->user->email
    );
    notificationToEmployees($notificationType, $auxData);
}


function notificationAsignacionAsesor($idClient, $idEmployee, $idGeneratorEmployee)
{
    /**
     * Ocurre cuando al asesor se le asigna automáticamente un asesor o manualmente
     * Esta notificación va destinada a Asesor con replicación y al cliente
     */

     $employee = Employee::find($idEmployee);
     $client = Client::find($idClient);
     $generatorEmployee = Employee::find($idGeneratorEmployee);

     $auxData = array(
        "id_client"                     =>  $client->id,
        "email_client"                  =>  $client->user->email,
        "name_client"                   =>  $client->names,
        "surname_client"                =>  $client->surnames,
        "name_employee"                 =>  $employee->name,
        "surname_employee"              =>  $employee->surname,
        "name_generator_employee"       =>  $generatorEmployee->name,
        "surname_generator_employee"    =>  $generatorEmployee->surname
    );

    $notificationTypeAdviser = NotificationType::where('slug', 'asignacion.asesor.adviser')->first();
    notificationToAdviser($notificationTypeAdviser, $employee->id, $auxData, false);

    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData);
    }

    $notificationTypeClient = NotificationType::where('slug', 'asignacion.asesor.client')->first();
    notificationToClient($notificationTypeClient, $employee->id, $auxData, false);
}


function notificationSolicitudCliente(Client $client, $serviceSlug)
{
    /*
    * Ocurre cuando un cliente hace alguna solicitud (Request)
    * Esta Notificación va destinada a: Asesor
    * Posee replicación
    */

    $assignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();

    $nameAdviser = ['', ''];
    if($assignedAdvisor)
    {
        $adviser = Employee::find($assignedAdvisor->employee_id);
        $nameAdviser[0] = $adviser->name;
        $nameAdviser[1] = $adviser->surname;
    }

    $auxData = array(
        "id_client"         =>  $client->id,
        "email_client"      =>  $client->user->email,
        "name_client"       =>  $client->names,
        "surname_client"    =>  $client->surnames,
        "name_employee"     =>  $nameAdviser[0],
        "surname_employee"  =>  $nameAdviser[1]
    );

    $notificationTypeAdviser;

    switch($serviceSlug)
    {
        case "plan.registrado":         $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.registrado.adviser')->first();
                                        break;

        case "plan.lite":               $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.lite.adviser')->first();
                                        break;

        case "plan.estandar.menor":
        case "plan.estandar.mayor":     $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.standard.adviser')->first();
                                        break;

        case "plan.premium.menor":
        case "plan.premium.mayor":      $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.premium.adviser')->first();
                                        break;

        case "analisis.financiero.juridico.plan.lite":  $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.plan.lite.adviser')->first();
                                                break;

        case "analisis.financiero.juridico.x1.plan.estandar":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack1.plan.standard.adviser')->first();
                                                                break;

        case "analisis.financiero.juridico.x3.plan.estandar":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack3.plan.standard.adviser')->first();
                                                                break;

        case "analisis.financiero.juridico.x5.plan.estandar":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack5.plan.standard.adviser')->first();
                                                                break;

        case "analisis.financiero.juridico.x1.plan.premium":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack1.plan.premium.adviser')->first();
                                                                break;

        case "analisis.financiero.juridico.x3.plan.premium":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack3.plan.premium.adviser')->first();
                                                                break;

        case "analisis.financiero.juridico.x5.plan.premium":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack5.plan.premium.adviser')->first();
                                                                break;


        case "plan.fase.evaluacion":    $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.fase.evaluacion.adviser')->first();
                                        break;

        case "plan.fase.analisis":      $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.fase.analisis.adviser')->first();
                                        break;

        case "plan.fase.ejecucion":     $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.fase.ejecucion.adviser')->first();
                                        break;

        case "plan.fase.asesoramiento.integral":    $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.fase.asesoramiento.integral.adviser')->first();
                                                    break;

        case "analisis.financiero.juridico.x1.plan.fase.analisis":  $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.analisis.financiero.pack1.plan.fase.analisis.adviser')->first();
                                                                    break;

        case "extranjeria.primera.residencia":              $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.extranjeria.primera.res.adviser')->first();
                                                            break;

        case "extranjeria.renovacion.primera.residencia":   $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.extranjeria.renovacion.primera.res.adviser')->first();
                                                            break;

        case "extranjeria.ciudadania":                      $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.extranjeria.ciudadania.adviser')->first();
                                                            break;

        case "plan.anual":      $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.anual.adviser')->first();
                                break;

        case "plan.mensual":    $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.cliente.plan.mensual.adviser')->first();
                                break;

    }

    if($assignedAdvisor)
    {
        notificationToAdviser($notificationTypeAdviser, $assignedAdvisor->employee_id, $auxData, false);
    }

    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData);
    }
}

function notificationVideoCall(Client $client, VideoCall $videoCall, VideoCallType $videoCallType)
{
    /*
    * Ocurre cuando un cliente solicita una Videollamada, no importa el tipo de la misma
    * Esta Notificación va destinada a: Asesor
    * Posee replicación
    */

    $assignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();
    $adviser = Employee::find($assignedAdvisor->employee_id);

    $auxData = array(
        "id_client"         =>  $client->id,
        "email_client"      =>  $client->user->email,
        "name_client"       =>  $client->names,
        "surname_client"    =>  $client->surnames,
        "name_employee"     =>  $adviser->name,
        "surname_employee"  =>  $adviser->surname,
        "video_call_type"   =>  $videoCallType->name
    );

    if($videoCall->list_of_business)
        $auxData["business_id"] = $videoCall->list_of_business;

    $notificationTypeAdviser = NotificationType::where('slug', 'solicitud.videollamada.cliente.adviser')->first();

    notificationToAdviser($notificationTypeAdviser, $assignedAdvisor->employee_id, $auxData, false);

    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData);
    }
}

function notificationCambioPlanExtranjeriaCliente(Client $client, $roleClient)
{
    /*
    * Ocurre cuando a un cliente se le cambia el plan
    * Esta notificación va destinada al Cliente
    * Recordando que el rol del cliente está asociado a su plan
    */

    $auxData = array(
        "id_client"         =>  $client->id,
        "email_client"      =>  $client->user->email,
        "name_client"       =>  $client->names,
        "surname_client"    =>  $client->surnames,
    );

    $notificationType;

    switch($roleClient)
    {
        case "usuario.premium.mayor":   $notificationType = NotificationType::where('slug', 'cambio.plan.premium')->first();
                                        break;

        case "usuario.premium.menor":   $notificationType = NotificationType::where('slug', 'cambio.plan.premium')->first();
                                        break;

        case "usuario.estandar.mayor":  $notificationType = NotificationType::where('slug', 'cambio.plan.standard')->first();
                                        break;

        case "usuario.estandar.menor":  $notificationType = NotificationType::where('slug', 'cambio.plan.standard')->first();
                                        break;

        case "usuario.lite":            $notificationType = NotificationType::where('slug', 'cambio.plan.lite')->first();
                                        break;

        case "cliente.registrado":      $notificationType = NotificationType::where('slug', 'cambio.plan.registrado')->first();
                                        break;

        case "cliente.fase.evaluacion":     $notificationType = NotificationType::where('slug', 'cambio.plan.fase.evaluacion')->first();
                                            break;

        case "cliente.fase.analisis":       $notificationType = NotificationType::where('slug', 'cambio.plan.fase.analisis')->first();
                                            break;

        case "cliente.fase.ejecucion":      $notificationType = NotificationType::where('slug', 'cambio.plan.fase.ejecucion')->first();
                                            break;

        case "cliente.fase.asesoramiento.integral":    $notificationType = NotificationType::where('slug', 'cambio.plan.fase.asesoramiento.integral')->first();
                                                       break;

        case "cliente.extranjeria.primera.residencia":      $notificationType = NotificationType::where('slug', 'cambio.extranjeria.primera.res')->first();
                                                            break;

        case "cliente.extranjeria.renovacion.residencia":   $notificationType = NotificationType::where('slug', 'cambio.extranjeria.renovacion.primera.res')->first();
                                                            break;

        case "cliente.extranjeria.ciudadania":    $notificationType = NotificationType::where('slug', 'cambio.plan.extranjeria.ciudadania')->first();
                                                  break;

        case "cliente.mensual":     $notificationType = NotificationType::where('slug', 'cambio.plan.mensual')->first();
                                    break;


        case "cliente.anual":       $notificationType = NotificationType::where('slug', 'cambio.plan.anual')->first();
                                    break;

        case "cliente.free.trial":      $notificationType = NotificationType::where('slug', 'cambio.plan.anual')->first();
                                        break;
    }

    notificationToClient($notificationType, null, $auxData, false);
}

function notificationPagoCargado(Client $client, Payment $payment, $act = false)
{
    /**
     * Ocurre cuando un empleado carga un pago de algún cliente
     * Este método está orientado a: Asesor, Empleado y Cliente
     * $act se refiere si es una actualización
     */

    $assignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();

    $nameAdviser = ['', ''];

    if($assignedAdvisor)
    {
        $adviser = Employee::find($assignedAdvisor->employee_id);
        $nameAdviser[0] = $adviser->name;
        $nameAdviser[1] = $adviser->surname;
    }

    $auxData = array(
        "id_client"         =>  $client->id,
        "email_client"      =>  $client->user->email,
        "name_client"       =>  $client->names,
        "surname_client"    =>  $client->surnames,
        "name_employee"     =>  $nameAdviser[0],
        "surname_employee"  =>  $nameAdviser[1],
        "payment_date"      =>  $payment->created_at->format('d-m-Y H:i:s'),
        "payment_observation"   => substr($payment->observation, 0, 128)
    );

    $notificationTypeAdviser = NotificationType::where('slug', 'pago.cargado.adviser')->first();

    if($assignedAdvisor)
    {
        notificationToAdviser($notificationTypeAdviser, $assignedAdvisor->employee_id, $auxData, $act);
    }


    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData, $act);
    }

    $notificationTypeClient = NotificationType::where('slug', 'pago.cargado.client')->first();
    notificationToClient($notificationTypeClient, null, $auxData, $act);
}

function notificationAnalisisFinancieroOtorgado($idClient, FinancialAnalysis $financialAnalysis)
{
    /**
     * Ocurre cuando a un cliente le otorgan Análisis Finacieros
     * Ya sea porque compró un pack o se cambió de Plan y este nuevo plan tiene Análisis Financieros Precargados
     */

    $client = Client::find($idClient);
    $assignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();
    $adviser = Employee::find($assignedAdvisor->employee_id);

    $auxData = array(
        "id_client"         =>  $client->id,
        "email_client"      =>  $client->user->email,
        "name_client"       =>  $client->names,
        "surname_client"    =>  $client->surnames,
        "name_employee"     =>  $adviser->name,
        "surname_employee"  =>  $adviser->surname,
        "number_financial_analysis"  =>  $financialAnalysis->addedService->service->financial_analysis_available
    );

    $notificationTypeAdviser = NotificationType::where('slug', 'analisis.financiero.otorgado.adviser')->first();
    notificationToAdviser($notificationTypeAdviser, $assignedAdvisor->employee_id, $auxData);

    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData);
    }

    $notificationTypeClient = NotificationType::where('slug', 'analisis.financiero.otorgado.client')->first();
    notificationToClient($notificationTypeClient, null, $auxData);

}

function notificationNegocioCumpleConPreferenciasDelCliente($idClient, $business)
{
    /**
     * Ocurre cuando un negocio cumple con las preferencias de un cliente
     */

    $client = Client::find($idClient);

    $auxData = array(
        "id_client"          =>  $idClient,
        "business_id_code"   =>  $business->id_code,
        // TODO: Aquí debería calcularse con env('APP_URL')
        "business_url"  => 'https://videoportaldenegocios.es/portal-negocios/' . $business->id,
    );

    $notificationTypeClient = NotificationType::where('slug', 'negocio.cumple.preferencia.cliente')->first();
    notificationToClient($notificationTypeClient, null, $auxData);
}

function notificationPlanExpiration($object)
{
    /*
    * Ocurre cuando un Plan de Videoportal de Negocios está recién culminado o próximo a culminar
    * Esta Notificación va destinada a: Asesor - Empleado - Cliente
    * Posee replicación
    */

    $client = Client::find($object->id_client);

    $assignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();

    $nameAdviser = ['', ''];
    if($assignedAdvisor)
    {
        $adviser = Employee::find($assignedAdvisor->employee_id);
        $nameAdviser[0] = $adviser->name;
        $nameAdviser[1] = $adviser->surname;
    }


    $auxData = array(
        "id_client"             =>  $client->id,
        "email_client"          =>  $client->user->email,
        "name_client"           =>  $client->names,
        "surname_client"        =>  $client->surnames,
        "name_employee"         =>  $nameAdviser[0],
        "surname_employee"      =>  $nameAdviser[1],
        "name_plan"             =>  $object->namePlan,
        "plan_expiration_date"  =>  $object->planExpirationDate
    );

    $notificationTypeAdviser;
    $notificationTypeClient;

    switch($object->notificationType)
    {
        case 'pre.notification':    $notificationTypeAdviser = NotificationType::where('slug', 'notificacion.previa.vencimiento.plan.adviser')->first();
                                    $notificationTypeClient = NotificationType::where('slug', 'notificacion.previa.vencimiento.plan.cliente')->first();
                break;

        case 'notification':        $notificationTypeAdviser = NotificationType::where('slug', 'notificacion.vencimiento.plan.adviser')->first();
                                    $notificationTypeClient = NotificationType::where('slug', 'notificacion.vencimiento.plan.cliente')->first();
                break;
    }

    if($assignedAdvisor)
        notificationToAdviser($notificationTypeAdviser, $assignedAdvisor->employee_id, $auxData, false);


    if($notificationTypeAdviser->replicate_notification)
    {
        $notificationTypeReplication = NotificationType::where('slug', $notificationTypeAdviser->replicate_notification)->first();
        notificationToEmployees($notificationTypeReplication, $auxData);
    }

    if($notificationTypeClient)
        notificationToClient($notificationTypeClient, null, $auxData, false);

}

function changeValuesTitleOrMessage($allData, NotificationType $notificationType)
{
    $title = $notificationType->title;
    $message = $notificationType->message;

    if(isset($allData['id_client']))
    {
        $title = str_replace('*id_client*', $allData['id_client'], $title);
        $message = str_replace('*id_client*', $allData['id_client'], $message);
    }

    if(isset($allData['email_client']))
    {
        $title = str_replace('*email_client*', $allData['email_client'], $title);
        $message = str_replace('*email_client*', $allData['email_client'], $message);
    }

    if(isset($allData['name_client']))
    {
        $title = str_replace('*name_client*', $allData['name_client'], $title);
        $message = str_replace('*name_client*', $allData['name_client'], $message);
    }

    if (strpos($title, '*name_client*') !== false)
        $title = str_replace('*name_client*', 'Sin_Nombre', $title);

    if (strpos($message, '*name_client*') !== false)
        $message = str_replace('*name_client*', 'Sin_Nombre', $message);

    if(isset($allData['surname_client']))
    {
        $title = str_replace('*surname_client*', $allData['surname_client'], $title);
        $message = str_replace('*surname_client*', $allData['surname_client'], $message);
    }

    if (strpos($title, '*surname_client*') !== false)
        $title = str_replace('*surname_client*', 'Sin_Apellido', $title);

    if (strpos($message, '*surname_client*') !== false)
        $message = str_replace('*surname_client*', 'Sin_Apellido', $message);


    if(isset($allData['name_employee']))
    {
        $title = str_replace('*name_employee*', $allData['name_employee'], $title);
        $message = str_replace('*name_employee*', $allData['name_employee'], $message);
    }

    if(isset($allData['surname_employee']))
    {
        $title = str_replace('*surname_employee*', $allData['surname_employee'], $title);
        $message = str_replace('*surname_employee*', $allData['surname_employee'], $message);
    }

    if(isset($allData['name_generator_employee']))
    {
        $title = str_replace('*name_generator_employee*', $allData['name_generator_employee'], $title);
        $message = str_replace('*name_generator_employee*', $allData['name_generator_employee'], $message);
    }

    if(isset($allData['surname_generator_employee']))
    {
        $title = str_replace('*surname_generator_employee*', $allData['surname_generator_employee'], $title);
        $message = str_replace('*surname_generator_employee*', $allData['surname_generator_employee'], $message);
    }

    if(isset($allData['business_id']))
    {
        $title = str_replace('*business_id*', $allData['business_id'], $title);
        $message = str_replace('*business_id*', $allData['business_id'], $message);
    }

    if (strpos($title, '*business_id*') !== false)
        $title = str_replace('*business_id*', 'Sin_ID_Negocio', $title);

    if (strpos($message, '*business_id*') !== false)
        $message = str_replace('*business_id*', 'No_Aplica_ID_del_Negocio', $message);

    if(isset($allData['business_id_code']))
    {
        $title = str_replace('*business_id_code*', $allData['business_id_code'], $title);
        $message = str_replace('*business_id_code*', $allData['business_id_code'], $message);
    }

    if (strpos($title, '*business_id_code*') !== false)
        $title = str_replace('*business_id_code*', 'Negocio sin Code Id', $title);

    if (strpos($message, '*business_id_code*') !== false)
        $message = str_replace('*business_id_code*', 'Negocio sin Code Id', $message);

    if(isset($allData['business_url']))
    {
        $title = str_replace('*business_url*', $allData['business_url'], $title);
        $message = str_replace('*business_url*', $allData['business_url'], $message);
    }

    if (strpos($title, '*business_url*') !== false)
        $title = str_replace('*business_url*', 'Sin_URL_Negocio', $title);

    if (strpos($message, '*business_url*') !== false)
        $message = str_replace('*business_url*', 'No_Aplica_URL_del_Negocio', $message);

    if(isset($allData['video_call_type']))
    {
        $title = str_replace('*video_call_type*', $allData['video_call_type'], $title);
        $message = str_replace('*video_call_type*', $allData['video_call_type'], $message);
    }

    if(isset($allData['payment_id']))
    {
        $title = str_replace('*payment_id*', $allData['payment_id'], $title);
        $message = str_replace('*payment_id*', $allData['payment_id'], $message);
    }

    if(isset($allData['payment_date']))
    {
        $title = str_replace('*payment_date*', $allData['payment_date'], $title);
        $message = str_replace('*payment_date*', $allData['payment_date'], $message);
    }

    if(isset($allData['payment_observation']))
    {
        $title = str_replace('*payment_observation*', $allData['payment_observation'], $title);
        $message = str_replace('*payment_observation*', $allData['payment_observation'], $message);
    }

    if(isset($allData['number_financial_analysis']))
    {
        $title = str_replace('*number_financial_analysis*', $allData['number_financial_analysis'], $title);
        $message = str_replace('*number_financial_analysis*', $allData['number_financial_analysis'], $message);
    }

    if(isset($allData['name_plan']))
    {
        $title = str_replace('*name_plan*', $allData['name_plan'], $title);
        $message = str_replace('*name_plan*', $allData['name_plan'], $message);
    }

    if(isset($allData['plan_expiration_date']))
    {
        $title = str_replace('*plan_expiration_date*', $allData['plan_expiration_date'], $title);
        $message = str_replace('*plan_expiration_date*', $allData['plan_expiration_date'], $message);
    }

    return [$title, $message];
}


function notificationToAdviser(NotificationType $notificationTypeAdviser, $idEmployee, $auxData, $actMsg = false)
{
    /**
     * Código repetitivo para enviar notificación a los asesores
     */

    if($notificationTypeAdviser->active)
    {
        $auxRetorno = changeValuesTitleOrMessage($auxData, $notificationTypeAdviser);

        $notification = new Notification();
        $notification->employee_id = $idEmployee;
        $notification->notification_type_id = $notificationTypeAdviser->id;
        $notification->title = $auxRetorno[0];
        if($actMsg)
            $notification->message = "(ACTUALIZADO) - " . $auxRetorno[1];
        else
            $notification->message = $auxRetorno[1];
        $notification->client_id = $auxData["id_client"];

        if(isset($auxData["business_url"]))
            $notification->url = $auxData["business_url"];

        $notification->save();
    }
}

function notificationToEmployees($notificationTypeEmployees, $auxData, $actMsg = false)
{
    /**
     * Código repetitivo para enviar notificación de réplica para empleados
     */

    if($notificationTypeEmployees->active)
    {
        $auxRetorno = changeValuesTitleOrMessage($auxData, $notificationTypeEmployees);

        $employees = Employee::leftJoin('users', 'employees.user_id', '=', 'users.id')
                            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                            ->whereIn('roles.id', explode(',', $notificationTypeEmployees->roles_id))
                            ->select('employees.id as idEmployee')
                            ->get();

        for($i = 0; $i < count($employees); $i++) {
            $notification = new Notification();
            $notification->employee_id = $employees[$i]['idEmployee'];
            $notification->notification_type_id = $notificationTypeEmployees->id;
            $notification->title = $auxRetorno[0];
            if($actMsg)
                $notification->message = "(ACTUALIZADO) - " . $auxRetorno[1];
            else
                $notification->message = $auxRetorno[1];
            $notification->client_id = $auxData["id_client"];

            if(isset($auxData["business_url"]))
                $notification->url = $auxData["business_url"];

            $notification->save();
        }
    }
}

function notificationToClient($notificationTypeClient, $idEmployee, $auxData, $actMsg = false)
{
    /**
     * Código repetitivo para enviar notifaciones a los clientes
     */

    if($notificationTypeClient->active)
    {
        if(!is_Null($auxData))
            $auxRetorno = changeValuesTitleOrMessage($auxData, $notificationTypeClient);

        $notification = new Notification();

        if(!is_Null($idEmployee))
            $notification->employee_id = $idEmployee;

        $notification->notification_type_id = $notificationTypeClient->id;

        $notification->title = (is_Null($auxData)) ? $notificationTypeClient->title : $auxRetorno[0];
        $notification->message = (is_Null($auxData)) ? $notificationTypeClient->message : $auxRetorno[1];
        if($actMsg)
            $notification->message = "(ACTUALIZADO) - " . $notification->message;


        $notification->client_id = $auxData["id_client"];

        if(isset($auxData["business_url"]))
            $notification->url = $auxData["business_url"];

        $notification->save();
    }
}
