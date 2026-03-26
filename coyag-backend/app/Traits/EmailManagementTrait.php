<?php

namespace App\Traits;

use \stdClass;
use App\Models\Client;
use App\Models\EmailManagement;
use App\Mail\NoActivityMail;
use App\Mail\PlanExpirationMail;
use App\Mail\PlanExpirationPreMail;
use App\Mail\VisaRequirementUpdateMail;
use App\Mail\VisaStepUpdateMail;
use App\Mail\WelcomeEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait EmailManagementTrait
{

    private function saveEmailSend($clientId, string $type): void
    {
        // TODO: Cuando se use una versión mayor a PHP 8.1, type debe trabajarse con enums
        /**
         * Guarda en BD el envío del email
         */
        $employeeId = (isset(Auth::user()->employee->id))   ? Auth::user()->employee->id
                                                            : 1;

        $emailManagement = new EmailManagement();
        $emailManagement->client_id = $clientId;
        $emailManagement->employee_id = $employeeId;
        $emailManagement->type = $type;
        $emailManagement->is_send = true;
        $emailManagement->sending_date = Carbon::now()->toDateTimeString();
        $emailManagement->save();

        addClientTimeline($clientId, $employeeId, 'Email Management', 'send', false);

    }

    public function sendWelcomeEmail($typeOfService, Client $client) : bool
    {
        /**
         * Email de Bienvenida
         * Para clientes de Extranjería y de Videoportal
         */

        $password = 'password';
        $linkWebApp = '';
        $serviceName = '';
        $title = '';
        $pathView = '';

        try {

            // TODO: Estas direcciones deberían ser relativas o cargarse del .env
            switch ($typeOfService) {
                case 'Inmigration': $linkWebApp = 'https://cohenyaguirre.es/intranet';
                                    $serviceName = 'Servicio de Extranjería';
                                    $title = 'Bienvenido(a) a Cohen&Aguirre';
                                    $pathView = 'emails.welcome.immigration';
                                    Mail::mailer('immigration')->to($client->user->email)->send(new WelcomeEmail($client, $password, $linkWebApp, $serviceName, $title, $pathView));
                                    break;

                case 'Plan':        $linkWebApp = 'https://videoportaldenegocios.es/videoportal';
                                    $serviceName = 'VIDEOPORTAL de Negocios';
                                    $title = 'Bienvenido(a) a VIDEOPORTAL de Negocios';
                                    if(checkIfTheUserHasTheRole(['cliente.free.trial'], $client->user->getRoles(), 'slug') && !empty($client->phone_mobile))
                                        $password = str_replace('+', '', $client->phone_mobile);
                                    $pathView = 'emails.welcome.videoportal';
                                    Mail::to($client->user->email)->send(new WelcomeEmail($client, $password, $linkWebApp, $serviceName, $title, $pathView));
                                    break;

                default:            $linkWebApp = '';
            }

            $this->saveEmailSend($client->id, 'welcome');

            return true;

        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");

            return false;
        }

    }

    public function planExpirationNotification($addServ, $notificationType) : bool
    {

        /**
         * Email para :
         *  - Notificación de vencimiento de Plan (clientes y empleados)
         *  - Notificación Previa de vencimiento de Plan
         * Para email de clientes y Videoportal (Por ahora no se envía)
         * $notificationType puede ser 'notification'o 'pre.notification'
         */

         $object = new stdClass();
         $object->id_client = $addServ->id_client;
         $object->nameClient = $addServ->names_client;
         $object->surnameClient = $addServ->surnames_client;
         $object->emailClient = $addServ->email_user;
         $object->namePlan = $addServ->name_service;
         $object->slugPlan = $addServ->slug_service;
         $object->notificationType = $notificationType;

         $object->planExpirationDate = '';

         if ($addServ->slug_service == 'plan.anual')
             $object->planExpirationDate = Carbon::parse($addServ->created_at)->translatedFormat('d F');

         if ($addServ->slug_service == 'plan.mensual')
             $object->planExpirationDate = Carbon::parse($addServ->created_at)->translatedFormat('d');

         if ($addServ->slug_service == 'plan.free.trial')
             $object->planExpirationDate = Carbon::parse($addServ->created_at)->addDays(7)->translatedFormat('d F Y');


        notificationPlanExpiration($object);

        try {

            if ($notificationType == 'pre.notification')
            {
                Mail::to($object->emailClient)->send(new PlanExpirationMail($object,
                    'Sobre su Plan - Video Portal de Negocios',
                    'emails.plan_expiration.client')
                );

                $this->saveEmailSend($object->id_client, 'plan_expiration_prenotification');

                /*
                    TODO: Por ahora, no se envía la prenotificación al equipo de VideoPortal

                    Mail::to('info@videoportaldenegocios.es')->send(new PlanExpirationMail($object,
                        "Prealerta de vencimiento de {$object->namePlan} del cliente {$object->nameClient} - Video Portal de Negocios",
                        'emails.plan_expiration.employee')
                    );
                */

            }
            else
            {
                // $notificationType == 'notification'

                Mail::to($object->emailClient)->send(new PlanExpirationMail($object,
                    'Sobre su Plan - Video Portal de Negocios',
                    'emails.plan_expiration.client')
                );

                $this->saveEmailSend($object->id_client, 'plan_expiration_notification');

                /*
                    TODO: Por ahora, no se envía la prenotificación al equipo de VideoPortal

                    Mail::to('info@videoportaldenegocios.es')->send(new PlanExpirationMail($object,
                        "Vencimiento de {$object->namePlan} del cliente {$object->nameClient} - Video Portal de Negocios",
                        'emails.plan_expiration.employee')
                    );
                */
            }

            return true;

        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");

            return false;
        }
    }


    public function visaStepUpdate (Client $client): void
    {
        try {
            Mail::mailer('immigration')->to($client->user->email)->send(new VisaStepUpdateMail($client->names));
            $this->saveEmailSend($client->id, 'visa_step_update');
        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");
        }
    }

    public function visaRequirementUpdate (Client $client): void
    {
        try {
            Mail::mailer('immigration')->to($client->user->email)->send(new VisaRequirementUpdateMail($client->names));
            $this->saveEmailSend($client->id, 'visa_requirement_update');
        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");
        }
    }

    public function noActivityImmigration (Client $client, $nameVisaType, $totalVS, $completeVS, $pendingDocsUpload): void
    {
        try {
            Mail::mailer('immigration')->to($client->user->email)->send(new NoActivityMail($client->names, $nameVisaType, $totalVS, $completeVS, $pendingDocsUpload));
            $this->saveEmailSend($client->id, 'no_activity_immigration');
        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");
        }
    }

}
