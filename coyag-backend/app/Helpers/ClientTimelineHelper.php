<?php

use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Employee;

function addClientTimeline($clientId, $employeeId, $module, $typeCrud, $madeByClient, $properties = null)
{
    /*
     * Esto es un timeline en base a las acciones sobre los clientes
     * Pero, soportará acciones que hace el propio cliente.
     * Para distinguirlas $madeByClient debe ser true y el employee_id será 1 (sistema) ya que este no puede ser null
     */

    $auxModule = "";

    switch($module)
    {
        case 'Auth':                $auxModule = 'Autenticación';
                                    break;

        case 'AddedService':        $auxModule = 'Servicio de Cliente';
                                    break;

        case "Assigned Advisor":    $auxModule = "Asesor Asignado";
                                    break;

        case "Business":            $auxModule = "Negocio";
                                    break;

        case "Client":              $auxModule = "Cliente"; //Solo actualización tipo de cliente
                                    break;

        case "Client Request":      $auxModule = "Petición del Cliente";
                                    break;

        case "Email Management":    $auxModule = "Envío de Email";
                                    break;

        case "Financial Analysis":          $auxModule = "Análisis Financiero";
                                            break;

        case "Payment":         $auxModule = "Pago";
                                break;

        case "Service":         $auxModule = "Servicio";
                                break;

        case "VideoCall":       $auxModule = "Videollamada";
                                break;

        default: $auxModule = $module;
    }

    $action = "";

    switch($typeCrud) {
        case "create":  $action = "creado";
                        break;

        case "update":  $action = "actualizado";
                        break;

        case "delete":  $action = "borrado";
                        break;

        case "cleaned":     $action = "limpiado";
                            break;

        case "associate":   $action = "asociado";
                            break;

        case "approve":     $action = "aprobado";
                            break;

        case "rejected":   $action = "rechazado";
                            break;

        case "send":        $action = "enviado";
                            break;

        case "show":        $action = 'visto';
                            break;

        case "list":        $action = 'listado';
                            break;

        default:        $action = $typeCrud;
    }

    $clientTimeline = new ClientTimeline();

    $clientTimeline->client_id = $clientId;
    $clientTimeline->employee_id = $employeeId;
    $clientTimeline->module = $auxModule; //Ahora está en español
    $clientTimeline->module_eng = $module; // Y ahora está en inglés 😂
    $clientTimeline->type_crud = $action; //Ahora está en español
    $clientTimeline->type_crud_eng = $typeCrud; // Y ahora está en inglés 😂
    $clientTimeline->properties = $properties;

    $employee = Employee::with("department")->where("employees.id", '=', $employeeId)->first();
    $client = Client::find($clientId);
    $emailClient = $client->user->email;

    if($madeByClient)
    {
        if($typeCrud == 'create')
        {   //Solo servirá si $typeCrud == create
            switch($module)
            {
                case 'Auth':        $clientTimeline->message = 'El cliente se ha autenticado';
                                    break;

                case 'Client':      $clientTimeline->message = "El cliente se ha registrado";
                                    break;

                case 'Client Request':  $clientTimeline->message = "El cliente ha realizado una petición";
                                        break;

                case 'VideoCall':   $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha solicitado una Videollamada";
                                    break;

                default: $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha $action en el módulo $auxModule";
            }
        }
        elseif ($typeCrud == 'delete') {
            switch($module)
            {
                case 'Auth':    $clientTimeline->message = 'El cliente ha cerrado sesión';
                                break;

                default:  $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha $action en el módulo $auxModule";
            }
        }
        elseif ($typeCrud == 'show') {
            switch($module)
            {
                case 'Business':    $clientTimeline->message = 'El cliente ha visto un negocio';
                                    break;

                default:  $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha $action en el módulo $auxModule";
            }
        }
        elseif ($typeCrud == 'list') {
            switch($module)
            {
                case 'Business':    $clientTimeline->message = 'El cliente ha listado los negocios';
                                    break;

                default:  $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha $action en el módulo $auxModule";
            }
        }
        else
            $clientTimeline->message = "El cliente $client->names $client->surnames (ID Cliente: $client->id) ha $action en el módulo $auxModule";

    }
    else
    {
        $departmentName = $employee->department->name;

        $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha $action en el módulo $auxModule sobre el cliente $client->names $client->surnames (ID: $client->id)";

        switch($module)
        {
            case 'AddedService':    switch($typeCrud)
                                    {
                                        case 'create'   :   $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha añadido el Servicio (Plan, Extranjería o AddOn) en el Sistema al cliente $client->names $client->surnames ($emailClient) (ID: $client->id)";
                                                            break;

                                        case 'update'   :   $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha actualizado el Servicio (Plan, Extranjería o AddOn) en el Sistema al cliente $client->names $client->surnames ($emailClient) (ID: $client->id)";
                                                            break;

                                        case 'delete'   :   $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha eliminado el Servicio (Plan, Extranjería o AddOn) en el Sistema al cliente $client->names $client->surnames ($emailClient) (ID: $client->id)";
                                                            break;

                                        default:    $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha $action en el módulo $auxModule sobre el cliente $client->names $client->surnames (ID: $client->id)";
                                    }
                                    break;


            case 'Client':  if($typeCrud == 'create')
                            {   //Solo servirá si $module = Client y $typeCrud == create
                                $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha registrado en el Sistema al cliente $emailClient (ID: $client->id)";
                                break;
                            }
                            break;

            case 'Client Request':  if($typeCrud == 'cleaned')
                                    {
                                        $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha desmarcado como atendido y limpiado la Petición del Cliente $emailClient (ID: $client->id)";
                                        break;
                                    }

                                    if($typeCrud == 'updated')
                                    {
                                        $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha marcado como atendido la Petición del Cliente $emailClient (ID: $client->id)";
                                        break;
                                    }

                                    break;

            case 'Financial Analysis':  if($typeCrud == 'create')
                                        {
                                            $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName le ha otorgado Análisis Financieros al cliente $client->names $client->surnames (ID: $client->id)";
                                            break;
                                        }

                                        break;

            case 'Email Management':    if($typeCrud == 'send')
                                        {
                                            $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName le ha enviado un Correo Electrónico al cliente $client->names $client->surnames (ID: $client->id)";
                                        }

                                        break;

            default:        $clientTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha $action en el módulo $auxModule sobre el cliente $client->names $client->surnames (ID: $client->id)";
        }

    }

    $clientTimeline->save();
}

