<?php

namespace App\Http\Controllers;

use App\Models\AutonomousCommunity;
use App\Models\AddedService;
use App\Models\Business;
use App\Models\ClientTimeline;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\FamilyType;
use App\Models\UpdateData;
use App\Models\Municipality;
use App\Models\NotificationType;
use App\Models\Province;
use App\Models\Sector;
use App\Models\Service;
use App\Models\UserCommentType;
use App\Models\VideoCallType;
use App\Models\VisaDocumentType;
use App\Models\VisaType;
use App\Services\MunicipalityUDService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDataController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
       /**
        * Almacena los cambios que debe sufrir la data.
        * Esta función está orientada a ejecutarse una vez en producción.
        */

        $code1 = 'DIC2020-1';

        if(UpdateData::where('code', $code1)->count() == 0)
        {

            //Actualizando a max bussiness_number
            $videoCallType = VideoCallType::where('slug', 'reuniones.equipo')->first();
            $videoCallType->business_number = 'max';
            if($videoCallType->save())
                Log::info("-----Actualizado Tipo de Videollamada-----");



            //Actualizando las notificaciones de videollamada
            $notificationType1 = NotificationType::where('slug', 'solicitud.videollamada.cliente.adviser')->first();
            $notificationType1->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado una Videollamada del tipo *video_call_type* sobre el (los) negocio(s) con Código(s): *business_id*.';
            if($notificationType1->save())
                Log::info("-----Actualizado Notificación Videollamada de Asesor-----");

            $notificationType2 = NotificationType::where('slug', 'solicitud.videollamada.cliente.employee')->first();
            $notificationType2->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado una Videollamada del tipo *video_call_type* sobre el (los) negocio(s) con Código(s): *business_id*. Asesor: *name_employee* *surname_employee*.';
            if($notificationType2->save())
                Log::info("-----Actualizado Notificación Videollamada de Empleados-----");


            //Actualizando los roles de usuario
            $role1 = config('roles.models.role')::where('slug', '=', 'usuario.premium')->first();
            $role1->name = 'Usuario Premium mayor 6 meses';
            $role1->slug = 'usuario.premium.mayor';
            $role1->description = 'Son aquellos usuarios registrados que han comprado el plan Premium y su llegada es mayor a 6 meses';
            if($role1->save())
                Log::info("-----Actualizado Rol Premium-----");

            $role2 = config('roles.models.role')::where('slug', '=', 'usuario.estandar')->first();
            $role2->name = 'Usuario Estándar mayor 6 meses';
            $role2->slug = 'usuario.estandar.mayor';
            $role2->description = 'Son aquellos usuarios registrados que han comprado el plan Estándar y su llegada es mayor a 6 meses';
            if($role2->save())
                Log::info("-----Actualizado Rol Estándar-----");

            $role3 = config('roles.models.role')::create([
                'name' => 'Usuario Premium menor 6 meses',
                'slug' => 'usuario.premium.menor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Premium y su llegada es menor a 6 meses',
                'level' => 2,
            ]);
            if($role3->save())
                Log::info("-----Creado Rol Premium-----");

            $role4 = config('roles.models.role')::create([
                'name' => 'Usuario Estándar menor 6 meses',
                'slug' => 'usuario.estandar.menor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Estándar y su llegada es menor a 6 meses',
                'level' => 2,
            ]);
            if($role4->save())
                Log::info("-----Creado Rol Estándar-----");



            $updateData = new UpdateData();
            $updateData->code = $code1;
            $updateData->save();

        }

        $code2 = 'DIC2020-2';

        if(UpdateData::where('code', $code2)->count() == 0)
        {

            //En este If iba la actualización de Document y DocumentType

            $updateData = new UpdateData();
            $updateData->code = $code2;
            $updateData->save();
        }


        $code3 = 'DIC2020-3';

        if(UpdateData::where('code', $code3)->count() == 0)
        {

            Service::create([
                'name'                          =>  'Plan Registrado',
                'slug'                          =>  'plan.registrado',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.registrado',
                'name_payment'                  =>  'Plan Registrado No Aplica Pago',
                'slug_payment'                  =>  'plan.registrado.no.aplica.pago',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Plan Lite',
                'slug'                          =>  'plan.lite',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.lite',
                'name_payment'                  =>  'Mensualidad Plan Lite',
                'slug_payment'                  =>  'mensualidad.plan.lite',
                'recommended_price'             =>  '100€ al mes',
                'flag_recurring_payment'        =>  true,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Plan Estándar mayor a 6 meses',
                'slug'                          =>  'plan.estandar.mayor',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.estandar.mayor',
                'name_payment'                  =>  'Pago Plan Estándar mayor a 6 meses',
                'slug_payment'                  =>  'pago.plan.estandar.mayor',
                'recommended_price'             =>  '6% o 4500 euros mínimo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  true
            ]);

            Service::create([
                'name'                          =>  'Plan Estándar menor a 6 meses',
                'slug'                          =>  'plan.estandar.menor',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.estandar.menor',
                'name_payment'                  =>  'Pago Plan Estándar menor a 6 meses',
                'slug_payment'                  =>  'pago.plan.estandar.menor',
                'recommended_price'             =>  '10% o 7500 Euros mínimo + IVA',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  true
            ]);

            Service::create([
                'name'                          =>  'Plan Premium mayor a 6 meses',
                'slug'                          =>  'plan.premium.mayor',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.premium.mayor',
                'name_payment'                  =>  'Pago Plan Premium mayor a 6 meses',
                'slug_payment'                  =>  'pago.plan.premium.mayor',
                'recommended_price'             =>  '8% o 6000 euros mínimo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  true
            ]);

            Service::create([
                'name'                          =>  'Plan Premium menor a 6 meses',
                'slug'                          =>  'plan.premium.menor',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'usuario.premium.menor',
                'name_payment'                  =>  'Pago Plan Premium menor a 6 meses',
                'slug_payment'                  =>  'pago.plan.premium.menor',
                'recommended_price'             =>  '12% o 9000 Euros mínimo + IVA',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  true
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico - Plan Lite',
                'slug'                          =>  'analisis.financiero.juridico.plan.lite',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.lite',
                'name_payment'                  =>  'Análisis Financiero-Jurídico - Plan Lite',
                'slug_payment'                  =>  'analisis.financiero.juridico.plan.lite',
                'recommended_price'             =>  '500€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Estándar',
                'slug'                          =>  'analisis.financiero.juridico.x1.plan.estandar',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Estándar',
                'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.estandar',
                'recommended_price'             =>  '1 x 1.500€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X3 - Plan Estándar',
                'slug'                          =>  'analisis.financiero.juridico.x3.plan.estandar',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X3 - Plan Estándar',
                'slug_payment'                  =>  'analisis.financiero.juridico.x3.plan.estandar',
                'recommended_price'             =>  '3 x 1.200€ c/estudio',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X5 - Plan Estándar',
                'slug'                          =>  'analisis.financiero.juridico.x5.plan.estandar',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X5 - Plan Estándar',
                'slug_payment'                  =>  'analisis.financiero.juridico.x5.plan.estandar',
                'recommended_price'             =>  '5 x 1.000€ c/estudio',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Premium',
                'slug'                          =>  'analisis.financiero.juridico.x1.plan.premium',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.premium.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Premium',
                'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.premium',
                'recommended_price'             =>  '1 x 1.500€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X3 - Plan Premium',
                'slug'                          =>  'analisis.financiero.juridico.x3.plan.premium',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.premium.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X3 - Plan Premium',
                'slug_payment'                  =>  'analisis.financiero.juridico.x3.plan.premium',
                'recommended_price'             =>  '3 x 1.200€ c/estudio',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X5 - Plan Premium',
                'slug'                          =>  'analisis.financiero.juridico.x5.plan.premium',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'usuario.premium.menor',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X5 - Plan Premium',
                'slug_payment'                  =>  'analisis.financiero.juridico.x5.plan.premium',
                'recommended_price'             =>  '5 x 1.000€ c/estudio',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Log::info("-----Agregado los Servicios Planes y AddOn-----");


            $notificationType = NotificationType::where('slug', 'resultado.pago.adviser')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug  resultado.pago.adviser -----');

            $notificationType = NotificationType::where('slug', 'resultado.pago.employee')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug resultado.pago.employee -----');

            $notificationType = NotificationType::where('slug', 'resultado.pago.client')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug resultado.pago.client -----');

            $notificationType = NotificationType::where('slug', 'resultado.peticion.analisis.financiero.adviser')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug  resultado.peticion.analisis.financiero.adviser -----');

            $notificationType = NotificationType::where('slug', 'resultado.peticion.analisis.financiero.employee')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug resultado.peticion.analisis.financiero.employee -----');

            $notificationType = NotificationType::where('slug', 'resultado.peticion.analisis.financiero.client')->first();
            if($notificationType->delete())
                Log::info('----- Borrado pago con slug resultado.peticion.analisis.financiero.client -----');

            $notificationType = NotificationType::where('slug', 'pago.revision.adviser')->first();
            $notificationType->name = 'Pago cargado - Copia Asesor';
            $notificationType->slug = 'pago.cargado.adviser';
            $notificationType->title = 'El pago de *email_client* *name_client* *surname_client* se ha cargado';
            $notificationType->message = 'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date* por concepto de *payment_name*.';
            $notificationType->replicate_notification = 'pago.cargado.employee';
            if($notificationType->save())
                Log::info('----- Actualizada la notificación con slug: pago.revision.adviser -----');

            $notificationType = NotificationType::where('slug', 'pago.revision.employee')->first();
            $notificationType->name = 'Pago cargado - Copia Empleado';
            $notificationType->slug = 'pago.cargado.employee';
            $notificationType->title = 'El pago de *email_client* *name_client* *surname_client* se ha cargado';
            $notificationType->message = 'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date* por concepto de *payment_name*. Asesor: *name_employee* *surname_employee*.';
            if($notificationType->save())
                Log::info('----- Actualizada la notificación con slug: pago.revision.employee -----');

            $notificationType = NotificationType::where('slug', 'pago.revision.client')->first();
            $notificationType->name = 'Pago cargado - Copia Cliente';
            $notificationType->slug = 'pago.cargado.client';
            $notificationType->title = 'Su pago se ha cargado';
            $notificationType->message = 'Se ha cargado el pago por concepto de *payment_name* cargado el *payment_date*.';
            if($notificationType->save())
                Log::info('----- Actualizada la notificación con slug: pago.revision.client -----');


            AddedService::create([
                'client_id'         =>      1,
                'service_id'        =>      4,
                'flag_active_plan'  =>      true
            ]);

            AddedService::create([
                'client_id'         =>      2,
                'service_id'        =>      3,
                'flag_active_plan'  =>      true
            ]);

            AddedService::create([
                'client_id'         =>      3,
                'service_id'        =>      2,
                'flag_active_plan'  =>      true
            ]);

            AddedService::create([
                'client_id'         =>      4,
                'service_id'        =>      1,
                'flag_active_plan'  =>      true
            ]);

            Log::info('----- Agregando "Relación" de los Servicios a los usuarios ya cargados -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Plan Registrado - Asesor';
            $notificationType->slug = 'solicitud.cliente.plan.registrado.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado el plan Registrado';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Registrado.';
            $notificationType->replicate_notification = 'solicitud.cliente.plan.registrado.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.registrado.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Plan Registrado - Empleado';
            $notificationType->slug = 'solicitud.cliente.plan.registrado.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado el plan Registrado a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Registrado. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.registrado.employee -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 1 Análisis Financiero Plan Premium - Asesor';
            $notificationType->slug = 'solicitud.analisis.financiero.pack1.plan.premium.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Premium.';
            $notificationType->replicate_notification = 'solicitud.analisis.financiero.pack1.plan.premium.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack1.plan.premium.adviser -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 1 Análisis Financiero Plan Premium - Empleado';
            $notificationType->slug = 'solicitud.analisis.financiero.pack1.plan.premium.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Premium. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack1.plan.premium.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 3 Análisis Financieros Plan Premium - Asesor';
            $notificationType->slug = 'solicitud.analisis.financiero.pack3.plan.premium.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Premium.';
            $notificationType->replicate_notification = 'solicitud.analisis.financiero.pack3.plan.premium.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack3.plan.premium.adviser -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 3 Análisis Financieros Plan Premium - Empleado';
            $notificationType->slug = 'solicitud.analisis.financiero.pack3.plan.premium.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Premium. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack3.plan.premium.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 5 Análisis Financieros Plan Premium - Asesor';
            $notificationType->slug = 'solicitud.analisis.financiero.pack5.plan.premium.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Premium.';
            $notificationType->replicate_notification = 'solicitud.analisis.financiero.pack5.plan.premium.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack5.plan.premium.adviser -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud Pack 5 Análisis Financieros Plan Premium - Empleado';
            $notificationType->slug = 'solicitud.analisis.financiero.pack5.plan.premium.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Premium. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.analisis.financiero.pack5.plan.premium.employee -----');


            $updateData = new UpdateData();
            $updateData->code = $code3;
            $updateData->save();
        }


        $code = 'ENE2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            $service = Service::where('slug', 'plan.registrado')->first();
            $service->financial_analysis_available = 0;
            $service->save();
            Log::info('----- Modificado Servicio: plan.registrado - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'plan.lite')->first();
            $service->financial_analysis_available = 0;
            $service->save();
            Log::info('----- Modificado Servicio: plan.lite - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'plan.estandar.mayor')->first();
            $service->financial_analysis_available = 2;
            $service->save();
            Log::info('----- Modificado Servicio: plan.estandar.mayor - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'plan.estandar.menor')->first();
            $service->financial_analysis_available = 1;
            $service->save();
            Log::info('----- Modificado Servicio: plan.estandar.menor - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'plan.premium.mayor')->first();
            $service->financial_analysis_available = -1;
            $service->save();
            Log::info('----- Modificado Servicio: plan.premium.mayor - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'plan.premium.menor')->first();
            $service->financial_analysis_available = 3;
            $service->save();
            Log::info('----- Modificado Servicio: plan.premium.menor - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.plan.lite')->first();
            $service->financial_analysis_available = 1;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.plan.lite - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x1.plan.estandar')->first();
            $service->financial_analysis_available = 1;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x1.plan.estandar - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x3.plan.estandar')->first();
            $service->financial_analysis_available = 3;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x3.plan.estandar - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x5.plan.estandar')->first();
            $service->financial_analysis_available = 5;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x5.plan.estandar - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x1.plan.premium')->first();
            $service->financial_analysis_available = 1;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x1.plan.premium - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x3.plan.premium')->first();
            $service->financial_analysis_available = 3;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x3.plan.premium - Cantidad de Análisis financiero-----');

            $service = Service::where('slug', 'analisis.financiero.juridico.x5.plan.premium')->first();
            $service->financial_analysis_available = 5;
            $service->save();
            Log::info('----- Modificado Servicio: analisis.financiero.juridico.x5.plan.premium - Cantidad de Análisis financiero-----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Análisis Financieros otorgado - Copia Asesor';
            $notificationType->slug = 'analisis.financiero.otorgado.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = 'Al cliente *email_client* *name_client* *surname_client* se le ha otorgado Análisis Financieros';
            $notificationType->message = 'Al cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se le ha otorgado *number_financial_analysis* Análisis Financieros.';
            $notificationType->replicate_notification = 'analisis.financiero.otorgado.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: analisis.financiero.otorgado.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Análisis Financieros otorgado - Copia Empleado';
            $notificationType->slug = 'analisis.financiero.otorgado.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = 'Al cliente *email_client* *name_client* *surname_client* se le ha otorgado Análisis Financieros';
            $notificationType->message = 'Al cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se le ha otorgado *number_financial_analysis* Análisis Financieros. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '1,2,4,5,11,12,13';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: analisis.financiero.otorgado.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Análisis Financieros otorgado - Copia Cliente';
            $notificationType->slug = 'analisis.financiero.otorgado.client';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Tiene Análisis Financieros disponibles';
            $notificationType->message = 'Ahora tiene *number_financial_analysis* Análisis Financieros.';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: analisis.financiero.otorgado.client -----');



            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'FEB2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            Service::where('slug', '<>', 'plan.registrado')
                        ->update(['flag_mandatory_payment' => 1]);

            Log::info('----- Actualizado el flag de pago obligatorio de todos los servicios a true, a excepción del Plan Registrado -----');

            $notificationType = NotificationType::where('slug', 'pago.cargado.adviser')->first();
            $notificationType->message = 'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date*, observación: *payment_observation*.';
            $notificationType->save();
            Log::info('----- Actualizado NotificationType con slug: pago.cargado.adviser -----');

            $notificationType = NotificationType::where('slug', 'pago.cargado.employee')->first();
            $notificationType->message = 'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date*, observación: *payment_observation*. Asesor: *name_employee* *surname_employee*.';
            $notificationType->save();
            Log::info('----- Actualizado NotificationType con slug: pago.cargado.employee -----');

            $notificationType = NotificationType::where('slug', 'pago.cargado.client')->first();
            $notificationType->message = 'Se ha cargado un pago el *payment_date*.';
            $notificationType->save();
            Log::info('----- Actualizado NotificationType con slug: pago.cargado.client -----');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'FEB2021-2';
        if(UpdateData::where('code', $code)->count() == 0)
        {
            UserCommentType::create(['name' => 'Extranjería']);
            UserCommentType::create(['name' => 'Marketing']);
            UserCommentType::create(['name' => 'Negocios']);
            Log::info('Agregado los 3 Tipos de Comentarios de Usuarios => Extranjería, Marketing y Negocios');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();

        }

        $code = 'ABR2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Continent::create(['name' => 'Sin continente']);
            Continent::create(['name' => 'África']);
            Continent::create(['name' => 'América']);
            Continent::create(['name' => 'Antártida']);
            Continent::create(['name' => 'Asia']);
            Continent::create(['name' => 'Europa']);
            Continent::create(['name' => 'Europa - UE']);
            Continent::create(['name' => 'Oceanía']);

            Log::info('Agregado los Continentes');

            $country = Country::find(1); $country->continent_id = 1; $country->save();
            $country = Country::find(2); $country->continent_id = 5; $country->save();
            $country = Country::find(3); $country->continent_id = 6; $country->save();
            $country = Country::find(4); $country->continent_id = 7; $country->save();
            $country = Country::find(5); $country->continent_id = 6; $country->save();
            $country = Country::find(6); $country->continent_id = 2; $country->save();
            $country = Country::find(7); $country->continent_id = 3; $country->save();
            $country = Country::find(8); $country->continent_id = 5; $country->save();
            $country = Country::find(9); $country->continent_id = 2; $country->save();
            $country = Country::find(10); $country->continent_id = 3; $country->save();
            $country = Country::find(11); $country->continent_id = 6; $country->save();
            $country = Country::find(12); $country->continent_id = 8; $country->save();
            $country = Country::find(13); $country->continent_id = 7; $country->save();
            $country = Country::find(14); $country->continent_id = 6; $country->save();
            $country = Country::find(15); $country->continent_id = 3; $country->save();
            $country = Country::find(16); $country->continent_id = 5; $country->save();
            $country = Country::find(17); $country->continent_id = 3; $country->save();
            $country = Country::find(18); $country->continent_id = 5; $country->save();
            $country = Country::find(19); $country->continent_id = 7; $country->save();
            $country = Country::find(20); $country->continent_id = 3; $country->save();
            $country = Country::find(21); $country->continent_id = 2; $country->save();
            $country = Country::find(22); $country->continent_id = 6; $country->save();
            $country = Country::find(23); $country->continent_id = 5; $country->save();
            $country = Country::find(24); $country->continent_id = 3; $country->save();
            $country = Country::find(25); $country->continent_id = 6; $country->save();
            $country = Country::find(26); $country->continent_id = 2; $country->save();
            $country = Country::find(27); $country->continent_id = 3; $country->save();
            $country = Country::find(28); $country->continent_id = 5; $country->save();
            $country = Country::find(29); $country->continent_id = 7; $country->save();
            $country = Country::find(30); $country->continent_id = 2; $country->save();
            $country = Country::find(31); $country->continent_id = 2; $country->save();
            $country = Country::find(32); $country->continent_id = 5; $country->save();
            $country = Country::find(33); $country->continent_id = 2; $country->save();
            $country = Country::find(34); $country->continent_id = 5; $country->save();
            $country = Country::find(35); $country->continent_id = 2; $country->save();
            $country = Country::find(36); $country->continent_id = 3; $country->save();
            $country = Country::find(37); $country->continent_id = 5; $country->save();
            $country = Country::find(38); $country->continent_id = 2; $country->save();
            $country = Country::find(39); $country->continent_id = 3; $country->save();
            $country = Country::find(40); $country->continent_id = 5; $country->save();
            $country = Country::find(41); $country->continent_id = 7; $country->save();
            $country = Country::find(42); $country->continent_id = 6; $country->save();
            $country = Country::find(43); $country->continent_id = 3; $country->save();
            $country = Country::find(44); $country->continent_id = 2; $country->save();
            $country = Country::find(45); $country->continent_id = 5; $country->save();
            $country = Country::find(46); $country->continent_id = 5; $country->save();
            $country = Country::find(47); $country->continent_id = 2; $country->save();
            $country = Country::find(48); $country->continent_id = 3; $country->save();
            $country = Country::find(49); $country->continent_id = 7; $country->save();
            $country = Country::find(50); $country->continent_id = 3; $country->save();
            $country = Country::find(51); $country->continent_id = 7; $country->save();
            $country = Country::find(52); $country->continent_id = 3; $country->save();
            $country = Country::find(53); $country->continent_id = 3; $country->save();
            $country = Country::find(54); $country->continent_id = 2; $country->save();
            $country = Country::find(55); $country->continent_id = 3; $country->save();
            $country = Country::find(56); $country->continent_id = 5; $country->save();
            $country = Country::find(57); $country->continent_id = 2; $country->save();
            $country = Country::find(58); $country->continent_id = 7; $country->save();
            $country = Country::find(59); $country->continent_id = 7; $country->save();
            $country = Country::find(60); $country->continent_id = 7; $country->save();
            $country = Country::find(61); $country->continent_id = 3; $country->save();
            $country = Country::find(62); $country->continent_id = 7; $country->save();
            $country = Country::find(63); $country->continent_id = 2; $country->save();
            $country = Country::find(64); $country->continent_id = 5; $country->save();
            $country = Country::find(65); $country->continent_id = 7; $country->save();
            $country = Country::find(66); $country->continent_id = 8; $country->save();
            $country = Country::find(67); $country->continent_id = 7; $country->save();
            $country = Country::find(68); $country->continent_id = 2; $country->save();
            $country = Country::find(69); $country->continent_id = 2; $country->save();
            $country = Country::find(70); $country->continent_id = 6; $country->save();
            $country = Country::find(71); $country->continent_id = 2; $country->save();
            $country = Country::find(72); $country->continent_id = 3; $country->save();
            $country = Country::find(73); $country->continent_id = 7; $country->save();
            $country = Country::find(74); $country->continent_id = 3; $country->save();
            $country = Country::find(75); $country->continent_id = 3; $country->save();
            $country = Country::find(76); $country->continent_id = 2; $country->save();
            $country = Country::find(77); $country->continent_id = 2; $country->save();
            $country = Country::find(78); $country->continent_id = 2; $country->save();
            $country = Country::find(79); $country->continent_id = 3; $country->save();
            $country = Country::find(80); $country->continent_id = 3; $country->save();
            $country = Country::find(81); $country->continent_id = 7; $country->save();
            $country = Country::find(82); $country->continent_id = 5; $country->save();
            $country = Country::find(83); $country->continent_id = 5; $country->save();
            $country = Country::find(84); $country->continent_id = 5; $country->save();
            $country = Country::find(85); $country->continent_id = 5; $country->save();
            $country = Country::find(86); $country->continent_id = 7; $country->save();
            $country = Country::find(87); $country->continent_id = 6; $country->save();
            $country = Country::find(88); $country->continent_id = 8; $country->save();
            $country = Country::find(89); $country->continent_id = 8; $country->save();
            $country = Country::find(90); $country->continent_id = 5; $country->save();
            $country = Country::find(91); $country->continent_id = 7; $country->save();
            $country = Country::find(92); $country->continent_id = 3; $country->save();
            $country = Country::find(93); $country->continent_id = 5; $country->save();
            $country = Country::find(94); $country->continent_id = 5; $country->save();
            $country = Country::find(95); $country->continent_id = 5; $country->save();
            $country = Country::find(96); $country->continent_id = 2; $country->save();
            $country = Country::find(97); $country->continent_id = 5; $country->save();
            $country = Country::find(98); $country->continent_id = 8; $country->save();
            $country = Country::find(99); $country->continent_id = 5; $country->save();
            $country = Country::find(100); $country->continent_id = 5; $country->save();
            $country = Country::find(101); $country->continent_id = 2; $country->save();
            $country = Country::find(102); $country->continent_id = 7; $country->save();
            $country = Country::find(103); $country->continent_id = 5; $country->save();
            $country = Country::find(104); $country->continent_id = 2; $country->save();
            $country = Country::find(105); $country->continent_id = 2; $country->save();
            $country = Country::find(106); $country->continent_id = 6; $country->save();
            $country = Country::find(107); $country->continent_id = 7; $country->save();
            $country = Country::find(108); $country->continent_id = 7; $country->save();
            $country = Country::find(109); $country->continent_id = 2; $country->save();
            $country = Country::find(110); $country->continent_id = 5; $country->save();
            $country = Country::find(111); $country->continent_id = 2; $country->save();
            $country = Country::find(112); $country->continent_id = 5; $country->save();
            $country = Country::find(113); $country->continent_id = 2; $country->save();
            $country = Country::find(114); $country->continent_id = 7; $country->save();
            $country = Country::find(115); $country->continent_id = 2; $country->save();
            $country = Country::find(116); $country->continent_id = 2; $country->save();
            $country = Country::find(117); $country->continent_id = 2; $country->save();
            $country = Country::find(118); $country->continent_id = 3; $country->save();
            $country = Country::find(119); $country->continent_id = 8; $country->save();
            $country = Country::find(120); $country->continent_id = 6; $country->save();
            $country = Country::find(121); $country->continent_id = 6; $country->save();
            $country = Country::find(122); $country->continent_id = 5; $country->save();
            $country = Country::find(123); $country->continent_id = 6; $country->save();
            $country = Country::find(124); $country->continent_id = 2; $country->save();
            $country = Country::find(125); $country->continent_id = 2; $country->save();
            $country = Country::find(126); $country->continent_id = 8; $country->save();
            $country = Country::find(127); $country->continent_id = 5; $country->save();
            $country = Country::find(128); $country->continent_id = 3; $country->save();
            $country = Country::find(129); $country->continent_id = 2; $country->save();
            $country = Country::find(130); $country->continent_id = 2; $country->save();
            $country = Country::find(131); $country->continent_id = 6; $country->save();
            $country = Country::find(132); $country->continent_id = 8; $country->save();
            $country = Country::find(133); $country->continent_id = 5; $country->save();
            $country = Country::find(134); $country->continent_id = 7; $country->save();
            $country = Country::find(135); $country->continent_id = 5; $country->save();
            $country = Country::find(136); $country->continent_id = 8; $country->save();
            $country = Country::find(137); $country->continent_id = 3; $country->save();
            $country = Country::find(138); $country->continent_id = 8; $country->save();
            $country = Country::find(139); $country->continent_id = 3; $country->save();
            $country = Country::find(140); $country->continent_id = 3; $country->save();
            $country = Country::find(141); $country->continent_id = 7; $country->save();
            $country = Country::find(142); $country->continent_id = 7; $country->save();
            $country = Country::find(143); $country->continent_id = 6; $country->save();
            $country = Country::find(144); $country->continent_id = 2; $country->save();
            $country = Country::find(145); $country->continent_id = 7; $country->save();
            $country = Country::find(146); $country->continent_id = 6; $country->save();
            $country = Country::find(147); $country->continent_id = 2; $country->save();
            $country = Country::find(148); $country->continent_id = 2; $country->save();
            $country = Country::find(149); $country->continent_id = 3; $country->save();
            $country = Country::find(150); $country->continent_id = 2; $country->save();
            $country = Country::find(151); $country->continent_id = 2; $country->save();
            $country = Country::find(152); $country->continent_id = 7; $country->save();
            $country = Country::find(153); $country->continent_id = 6; $country->save();
            $country = Country::find(154); $country->continent_id = 8; $country->save();
            $country = Country::find(155); $country->continent_id = 3; $country->save();
            $country = Country::find(156); $country->continent_id = 6; $country->save();
            $country = Country::find(157); $country->continent_id = 3; $country->save();
            $country = Country::find(158); $country->continent_id = 3; $country->save();
            $country = Country::find(159); $country->continent_id = 2; $country->save();
            $country = Country::find(160); $country->continent_id = 2; $country->save();
            $country = Country::find(161); $country->continent_id = 6; $country->save();
            $country = Country::find(162); $country->continent_id = 2; $country->save();
            $country = Country::find(163); $country->continent_id = 2; $country->save();
            $country = Country::find(164); $country->continent_id = 5; $country->save();
            $country = Country::find(165); $country->continent_id = 5; $country->save();
            $country = Country::find(166); $country->continent_id = 2; $country->save();
            $country = Country::find(167); $country->continent_id = 5; $country->save();
            $country = Country::find(168); $country->continent_id = 2; $country->save();
            $country = Country::find(169); $country->continent_id = 2; $country->save();
            $country = Country::find(170); $country->continent_id = 2; $country->save();
            $country = Country::find(171); $country->continent_id = 7; $country->save();
            $country = Country::find(172); $country->continent_id = 6; $country->save();
            $country = Country::find(173); $country->continent_id = 3; $country->save();
            $country = Country::find(174); $country->continent_id = 5; $country->save();
            $country = Country::find(175); $country->continent_id = 2; $country->save();
            $country = Country::find(176); $country->continent_id = 5; $country->save();
            $country = Country::find(177); $country->continent_id = 5; $country->save();
            $country = Country::find(178); $country->continent_id = 2; $country->save();
            $country = Country::find(179); $country->continent_id = 8; $country->save();
            $country = Country::find(180); $country->continent_id = 3; $country->save();
            $country = Country::find(181); $country->continent_id = 2; $country->save();
            $country = Country::find(182); $country->continent_id = 5; $country->save();
            $country = Country::find(183); $country->continent_id = 5; $country->save();
            $country = Country::find(184); $country->continent_id = 8; $country->save();
            $country = Country::find(185); $country->continent_id = 6; $country->save();
            $country = Country::find(186); $country->continent_id = 2; $country->save();
            $country = Country::find(187); $country->continent_id = 3; $country->save();
            $country = Country::find(188); $country->continent_id = 5; $country->save();
            $country = Country::find(189); $country->continent_id = 8; $country->save();
            $country = Country::find(190); $country->continent_id = 3; $country->save();
            $country = Country::find(191); $country->continent_id = 5; $country->save();
            $country = Country::find(192); $country->continent_id = 5; $country->save();
            $country = Country::find(193); $country->continent_id = 2; $country->save();
            $country = Country::find(194); $country->continent_id = 2; $country->save();
            $country = Country::find(195); $country->continent_id = 2; $country->save();

            Log::info('Actualizado continent_id en countries');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();

        }


        $code = 'MAY2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Fase de Evaluación',
                'slug'        => 'cliente.fase.evaluacion',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Evaluación (Servicio)',
                'level'       => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Fase de Evaluación-----");


            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Fase de Análisis',
                'slug'        => 'cliente.fase.analisis',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Fase de Análisis (Servicio)',
                'level'       => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Fase de Análisis-----");


            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Fase de Ejecución',
                'slug'        => 'cliente.fase.ejecucion',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Fase de Ejecución (Servicio)',
                'level'       => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Fase de Ejecución-----");


            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Fase de Asesoramiento Integral',
                'slug'        => 'cliente.fase.asesoramiento.integral',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Asesoramiento Integral (Servicio)',
                'level'       => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Fase Asesoramiento Integral-----");


            Service::where('type', 'Plan')
                    ->where('slug', '<>', 'plan.registrado')
                    ->update(['flag_active' => false]);

            Log::info("-----Los Servicios tipo Plan han sido desactivados-----");

            Service::create([
                'name'                          =>  'Plan Fase de Evaluación',
                'slug'                          =>  'plan.fase.evaluacion',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.fase.evaluacion',
                'name_payment'                  =>  'Pago Plan Fase de Evaluación',
                'slug_payment'                  =>  'pago.plan.fase.evaluacion',
                'recommended_price'             =>  '1000€ al año',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Agregado Servicio Plan Fase de Evaluación-----");

            Service::create([
                'name'                          =>  'Plan Fase de Análisis',
                'slug'                          =>  'plan.fase.analisis',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.fase.analisis',
                'name_payment'                  =>  'Pago Plan Fase de Análisis',
                'slug_payment'                  =>  'pago.plan.fase.analisis',
                'recommended_price'             =>  '2000€ al año',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  2,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Agregado Servicio Plan Fase de Análisis-----");

            Service::create([
                'name'                          =>  'Plan Fase de Ejecución',
                'slug'                          =>  'plan.fase.ejecucion',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.fase.ejecucion',
                'name_payment'                  =>  'Pago Plan Fase de Ejecución',
                'slug_payment'                  =>  'pago.plan.fase.ejecucion',
                'recommended_price'             =>  '3000€ al año',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Agregado Servicio Plan Fase de Ejecución-----");

            Service::create([
                'name'                          =>  'Plan Fase de Asesoramiento Integral',
                'slug'                          =>  'plan.fase.asesoramiento.integral',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.fase.asesoramiento.integral',
                'name_payment'                  =>  'Pago Plan Fase de Asesoramiento Integral',
                'slug_payment'                  =>  'pago.plan.fase.asesoramiento.integral',
                'recommended_price'             =>  '4000€ al año',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Agregado Servicio Plan Fase de Asesoramiento Integral-----");

            Service::create([
                'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Fase de Análisis',
                'slug'                          =>  'analisis.financiero.juridico.x1.plan.fase.analisis',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.fase.analisis',
                'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Fase de Análisis',
                'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.fase.analisis',
                'recommended_price'             =>  'Sin precio recomendado',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  1,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Agregado Servicio Análisis Financiero-Jurídico X1 - Plan Fase de Análisis-----");


            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Evaluación - Asesor',
                'slug'      =>  'solicitud.cliente.plan.fase.evaluacion.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Evaluación',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Evaluación.',
                'replicate_notification'  => 'solicitud.cliente.plan.fase.evaluacion.employee'
            ]);

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Evaluación - Empleado',
                'slug'      =>  'solicitud.cliente.plan.fase.evaluacion.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Evaluación a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Evaluación. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
            ]);

            NotificationType::create([
                'name'      =>  'Cambio a Plan Fase de Evaluación',
                'slug'      =>  'cambio.plan.fase.evaluacion',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Fase de Evaluación.'
            ]);

            Log::info("-----Agregado Tipo de Notificación para Petición y cambio de plan tipo Fase de Evaluación-----");


            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Análisis - Asesor',
                'slug'      =>  'solicitud.cliente.plan.fase.analisis.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Análisis',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Análisis.',
                'replicate_notification'  => 'solicitud.cliente.plan.fase.analisis.employee'
            ]);

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Análisis - Empleado',
                'slug'      =>  'solicitud.cliente.plan.fase.analisis.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Análisis a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Análisis. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
            ]);

            NotificationType::create([
                'name'      =>  'Cambio a Plan Fase de Análisis',
                'slug'      =>  'cambio.plan.fase.analisis',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Fase de Análisis.'
            ]);

            Log::info("-----Agregado Tipo de Notificación para Petición y cambio de plan tipo Fase de Análisis-----");


            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Ejecución - Asesor',
                'slug'      =>  'solicitud.cliente.plan.fase.ejecucion.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Ejecución',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Ejecución.',
                'replicate_notification'  => 'solicitud.cliente.plan.fase.ejecucion.employee'
            ]);

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Ejecución - Empleado',
                'slug'      =>  'solicitud.cliente.plan.fase.ejecucion.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Ejecución a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Ejecución. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
            ]);

            NotificationType::create([
                'name'      =>  'Cambio a Plan Fase de Ejecución',
                'slug'      =>  'cambio.plan.fase.ejecucion',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Fase de Ejecución.'
            ]);

            Log::info("-----Agregado Tipo de Notificación para Petición y cambio de plan tipo Fase de Ejecución-----");


            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Asesoramiento Integral - Asesor',
                'slug'      =>  'solicitud.cliente.plan.fase.asesoramiento.integral.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Asesoramiento Integral',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Asesoramiento Integral.',
                'replicate_notification'  => 'solicitud.cliente.plan.fase.asesoramiento.integral.employee'
            ]);

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Fase de Asesoramiento Integral - Empleado',
                'slug'      =>  'solicitud.cliente.plan.fase.asesoramiento.integral.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Fase de Asesoramiento Integral a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Fase de Asesoramiento Integral. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
            ]);

            NotificationType::create([
                'name'      =>  'Cambio a Plan Fase de Asesoramiento Integral',
                'slug'      =>  'cambio.plan.fase.asesoramiento.integral',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Fase de Asesoramiento Integral.'
            ]);

            Log::info("-----Agregado Tipo de Notificación para Petición y cambio de plan tipo Fase de Asesoramiento Integral-----");


            NotificationType::create([
                'name'      =>  'Solicitud Pack 1 Análisis Financiero-Jurídico Plan Fase de Análisis - Asesor',
                'slug'      =>  'solicitud.analisis.financiero.pack1.plan.fase.analisis.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero Jurídico',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero-Jurídico Plan Fase de Análisis.',
                'replicate_notification'  => 'solicitud.analisis.financiero.pack1.plan.fase.analisis.employee'
            ]);

            NotificationType::create([
                'name'      =>  'Solicitud Pack 1 Análisis Financiero-Jurídico Plan Fase de Análisis - Empleado',
                'slug'      =>  'solicitud.analisis.financiero.pack1.plan.fase.analisis.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero Jurídico a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero-Jurídico Plan Fase de Análisis. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13'
            ]);

            Log::info("-----Agregado Tipo de Notificación para Petición de Análisis Financiero-Jurídico para plan tipo Fase de Análisis-----");


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'MAY2021-2';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Extranjería Primera Residencia',
                'slug'        => 'cliente.extranjeria.primera.residencia',
                'description' => 'Son aquellos clientes que están en el proceso para obtener su Primera Residenca',
                'level'       => 2,
            ]);
            if($role->save()) Log::info("-----Creado Rol Cliente Extranjería Primera Residencia-----");


            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Extranjería Renovación de Primera Residencia',
                'slug'        => 'cliente.extranjeria.renovacion.residencia',
                'description' => 'Son aquellos clientes que están en el proceso de Renovación de su Primera Residencia',
                'level'       => 2,
            ]);
            if($role->save()) Log::info("-----Creado Rol Cliente Extranjería Renovación de Primera Residencia-----");


            $role = config('roles.models.role')::create([
                'name'        => 'Cliente Extranjería Ciudadanía',
                'slug'        => 'cliente.extranjeria.ciudadania',
                'description' => 'Son aquellos clientes que están en el proceso de Ciudadanía',
                'level'       => 2,
            ]);
            if($role->save()) Log::info("-----Creado Rol Cliente Extranjería Ciudadanía-----");


            Service::create([
                'name'                          =>  'Extranjería Primera Residencia',
                'slug'                          =>  'extranjeria.primera.residencia',
                'type'                          =>  'Inmigration',
                'roles_slug'                    =>  'cliente.extranjeria.primera.residencia',
                'name_payment'                  =>  'Pago Extranjería Primera Residencia',
                'slug_payment'                  =>  'pago.extranjeria.primera.residencia',
                'recommended_price'             =>  'N/A',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Creado Servicio Extranjería Primera Residencia-----");

            Service::create([
                'name'                          =>  'Extranjería Renovación de Primera Residencia',
                'slug'                          =>  'extranjeria.renovacion.primera.residencia',
                'type'                          =>  'Inmigration',
                'roles_slug'                    =>  'cliente.extranjeria.renovacion.residencia',
                'name_payment'                  =>  'Pago Extranjería renovación de Primera Residencia',
                'slug_payment'                  =>  'pago.extranjeria.renovacion.primera.residencia',
                'recommended_price'             =>  'N/A',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Creado Servicio Extranjería Renovación de Primera Residencia-----");

            Service::create([
                'name'                          =>  'Extranjería Ciudadanía',
                'slug'                          =>  'extranjeria.ciudadania',
                'type'                          =>  'Inmigration',
                'roles_slug'                    =>  'cliente.extranjeria.ciudadania',
                'name_payment'                  =>  'Pago Extranjería Ciudadanía',
                'slug_payment'                  =>  'pago.extranjeria.ciudadania',
                'recommended_price'             =>  'N/A',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false,
                'financial_analysis_available'  =>  0,
                'flag_mandatory_payment'        =>  true
            ]);
            Log::info("-----Creado Servicio Extranjería Ciudadanía-----");


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'JUN2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            //Actualizando los roles de usuario
            $role = config('roles.models.role')::where('slug', '=', 'usuario.registrado')->first();
            $role->name = 'Cliente Registrado';
            $role->slug = 'cliente.registrado';
            $role->description = 'Son aquellos clientes que se han registrado a la plataforma, pero no han realizado pago alguno o suscrito a algún servicio';
            if($role->save())
                Log::info("-----Actualizado Rol Usuario Registrado-----");

            $service = Service::find(1);
            $service->roles_slug = 'cliente.registrado';
            $service->flag_active = 0; //No estará activo porque ahora los clientes pueden estar sin plan
            if($service->save())
                Log::info("-----Actualizado Servicio Plan Registrado-----");


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'JUN2021-2';

        if(UpdateData::where('code', $code)->count() == 0)
        {


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Primera Residencia - Asesor';
            $notificationType->slug = 'solicitud.cliente.extranjeria.primera.res.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Primera Residencia';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Primera Residencia.';
            $notificationType->replicate_notification = 'solicitud.cliente.extranjeria.primera.res.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug:  solicitud.cliente.extranjeria.primera.res.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Primera Residencia - Empleado';
            $notificationType->slug = 'solicitud.cliente.extranjeria.primera.res.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Primera Residencia a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Primera Residencia. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13,14,15,16,17,18,5,6';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.extranjeria.primera.res.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Cambio a Extranjería Primera Residencia';
            $notificationType->slug = 'cambio.extranjeria.primera.res';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Ahora tienes un nuevo servicio de extranjería';
            $notificationType->message = 'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Primera Residencia.';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: cambio.extranjeria.primera.res -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Renovación Primera Residencia - Asesor';
            $notificationType->slug = 'solicitud.cliente.extranjeria.renovacion.primera.res.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Renovación Primera Residencia';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Renovación Primera Residencia.';
            $notificationType->replicate_notification = 'solicitud.cliente.extranjeria.renovacion.primera.res.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.extranjeria.renovacion.primera.res.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Renovación Primera Residencia - Empleado';
            $notificationType->slug = 'solicitud.cliente.extranjeria.renovacion.primera.res.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Renovación Primera Residencia';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Renovación Primera Residencia. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13,14,15,16,17,18,5,6';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.extranjeria.renovacion.primera.res.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Cambio a Extranjería Renovación Primera Residencia';
            $notificationType->slug = 'cambio.extranjeria.renovacion.primera.res';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Ahora tienes un nuevo servicio de extranjería';
            $notificationType->message = 'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Renovación Primera Residencia.';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: cambio.extranjeria.renovacion.primera.res -----');


            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Ciudadanía - Asesor';
            $notificationType->slug = 'solicitud.cliente.extranjeria.ciudadania.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Ciudadanía';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Ciudadanía.';
            $notificationType->replicate_notification = 'solicitud.cliente.extranjeria.ciudadania.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.extranjeria.ciudadania.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Solicitud de un cliente a Extranjería Ciudadanía - Empleado';
            $notificationType->slug = 'solicitud.cliente.extranjeria.ciudadania.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = '*email_client* *name_client* *surname_client* ha solicitado extranjería Ciudadanía a su asesor';
            $notificationType->message = 'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Ciudadanía. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '11,12,13,14,15,16,17,18,5,6';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: solicitud.cliente.extranjeria.ciudadania.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Cambio a Extranjería Ciudadanía';
            $notificationType->slug = 'cambio.plan.extranjeria.ciudadania';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Ahora tienes un nuevo servicio de extranjería';
            $notificationType->message = 'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Ciudadanía.';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: cambio.plan.extranjeria.ciudadania -----');


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'JUL2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            VisaType::create([
                'name'          =>  'Arraigo Social',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Asilo',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Razones Humanitarias',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Reagrupación - Comunitario',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Reagrupación - Residente',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Visado Estudiantes',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Visado Golden',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Visado Intraempresarial',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Visado No Lucrativo',
                'flag_active'   =>  1,
            ]);

            VisaType::create([
                'name'          =>  'Visado Profesional Altamente Cualificado',
                'flag_active'   =>  1,
            ]);

            Log::info("-----Agregando Data Fake de Tipos de Visado-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'JUL2021-2';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            FamilyType::create([
                'name'          =>  'Cónyugue'
            ]);

            FamilyType::create([
                'name'          =>  'Hijo'
            ]);

            FamilyType::create([
                'name'          =>  'Madre'
            ]);

            FamilyType::create([
                'name'          =>  'Padre'
            ]);

            FamilyType::create([
                'name'          =>  'Hermano'
            ]);

            FamilyType::create([
                'name'          =>  'Suegro'
            ]);

            FamilyType::create([
                'name'          =>  'Cuñado'
            ]);

            FamilyType::create([
                'name'          =>  'Sobrino'
            ]);

            FamilyType::create([
                'name'          =>  'Tío'
            ]);

            FamilyType::create([
                'name'          =>  'Primo'
            ]);

            Log::info("-----Agregado Tipos de Familiares-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'JUL2021-3';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            VisaDocumentType::create([
                'name'                  =>  'Formulario de solicitud de visado nacional',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Pasaporte',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Impreso de solicitud de autorización de RNL Modelo EX-01',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'IMPRESO Modelo 790 -> Justificativo de haber pagado todas las tasas',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Acreditación de recursos económicos',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Acta de Nacimiento',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Acta de Matrimonio',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Patria Potestad',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Autorización de los Padres',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Seguro Médico',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Certificado de Antecedentes Penales Apostillados',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Certificado Médico',
            ]);

            VisaDocumentType::create([
                'name'                  =>  'Constancia de Inscripción en la Universidad o Centro de Estudios',
            ]);

            Log::info("-----Agregando Tipos de Documentos de Visa-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'JUL2021-4';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            $visaType = VisaType::find(1);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(2);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(3);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(4);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(5);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(7);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(8);
            $visaType->flag_active = false;
            $visaType->save();

            $visaType = VisaType::find(10);
            $visaType->flag_active = false;
            $visaType->save();


            Log::info("-----Desactivando los Tipos de Visa que no se trabajarán por ahora-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'AGO2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $familyType = FamilyType::where('name', 'Cónyugue')->first();
            $familyType->name = 'Cónyuge';
            $familyType->save();

            Log::info('Solventando Bug, cambiando Cónyugue por Cónyuge');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'AGO2021-2';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            UserCommentType::create(['name' => 'VideoPortal']);
            Log::info("-----Agregando un nuevo Tipo de Comentario de Usuario-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = 'AGO2021-3';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            VisaType::where('flag_active', false)
                            ->update(['flag_active' => true]);
            Log::info("-----Ahora todos los tipos de visa están activos-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = 'SEP2021-1';

        if(UpdateData::where('code', $code)->count() == 0)
        {
            $service = Service::where('slug', 'extranjeria.renovacion.primera.residencia')->first();
            $service->name = "Extranjería Renovación de Residencia";
            $service->save();
            Log::info("-----Cambiando el Nombre de la segunda residencia, mas no su slug-----");

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "JUL2022-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $notificationType = new NotificationType();
            $notificationType->name = 'Negocio Cumple con Preferencia(s) del Cliente';
            $notificationType->slug = 'negocio.cumple.preferencia.cliente';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Hemos encontrado el Negocio Ideal para ti';
            $notificationType->message = 'El Negocio con ID: *business_id* - cumple con tus preferencias, puedes verlo en: *business_url*';

            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: negocio.cumple.preferencia.cliente -----');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "JUL2022-2";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $notificationType = NotificationType::where('slug', 'negocio.cumple.preferencia.cliente')->first();
            $notificationType->message = 'El Negocio con ID: *business_id_code* - cumple con tus preferencias, puedes verlo en: *business_url*';

            if($notificationType->save())
                Log::info('----- Editada la notificación con slug: negocio.cumple.preferencia.cliente -----');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = "NOV2022-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            $arrBusiness = Business::get();

            foreach($arrBusiness as $business) {
                if(!(empty($business->rental) || empty($business->size))){
                    $business->price_per_sqm = round($business->rental / $business->size, 2);
                    $business->save();
                }
            }

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "NOV2022-2";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $role = config('roles.models.role')::create([
                'name' => 'Cliente Anual',
                'slug' => 'cliente.anual',
                'description' => 'Son aquellos usuarios registrados que están pagando por el acceso al VideoPortal de forma anual',
                'level' => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Anual-----");

            $role = config('roles.models.role')::create([
                'name' => 'Cliente Mensual',
                'slug' => 'cliente.mensual',
                'description' => 'Son aquellos usuarios registrados que están pagando por el acceso al VideoPortal de forma mensual',
                'level' => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Mensual-----");


            Service::create([
                'name'                          =>  'Plan Anual',
                'slug'                          =>  'plan.anual',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Pago Plan Anual',
                'slug_payment'                  =>  'pago.plan.anual',
                'recommended_price'             =>  '900€ al año',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);
            Log::info("-----Creado Plan Anual-----");


            Service::create([
                'name'                          =>  'Plan Mensual',
                'slug'                          =>  'plan.mensual',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Pago Plan Mensual',
                'slug_payment'                  =>  'pago.plan.mensual',
                'recommended_price'             =>  '80€ al mes',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);
            Log::info("-----Creado Plan Mensual-----");


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "NOV2022-3";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Mensual - Asesor',
                'slug'      =>  'solicitud.cliente.plan.mensual.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Mensual',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Mensual.',
                'replicate_notification'    =>  'solicitud.cliente.plan.mensual.employee'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.mensual.adviser -----');

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan mensual - Empleado',
                'slug'      =>  'solicitud.cliente.plan.mensual.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Mensual a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Mensual. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.mensual.employee -----');

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Anual - Asesor',
                'slug'      =>  'solicitud.cliente.plan.anual.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Anual',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Anual.',
                'replicate_notification'    =>  'solicitud.cliente.plan.anual.employee'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.anual.adviser -----');

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Anual - Empleado',
                'slug'      =>  'solicitud.cliente.plan.anual.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Anual a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Anual. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.anual.employee -----');

            NotificationType::create([
                'name'      =>  'Cambio a Plan Mensual',
                'slug'      =>  'cambio.plan.mensual',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Mensual.'
            ]);
            Log::info('----- Agregada la notificación con slug: cambio.plan.mensual -----');

            NotificationType::create([
                'name'      =>  'Cambio a Plan Anual',
                'slug'      =>  'cambio.plan.anual',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el plan Anual.'
            ]);
            Log::info('----- Agregada la notificación con slug: cambio.plan.anual -----');


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "NOV2022-4";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Business::where('name', 'like' , 'Alquiler de Local en %')
                    ->update(["name" => DB::raw("REPLACE(name,  'Alquiler de Local en ', 'Local en traspaso en ')")]);

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = "ENE2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Sector::where('id', 1)
                        ->update(["name" => "Bar"]);

            Sector::where('id', 2)
                        ->update(["name" => "Alimentacion"]);

            Sector::where('id', 3)
                        ->update(["name" => "Restaurantes"]);

            Sector::where('id', 4)
                        ->update(["name" => "Papelerias"]);

            Sector::where('id', 5)
                        ->update(["name" => "Decoracion"]);

            Sector::where('id', 6)
                        ->update(["name" => "Panaderia y Pasteleria"]);

            Sector::where('id', 7)
                        ->update(["name" => "Estetica"]);

            Sector::where('id', 8)
                        ->update(["name" => "Peluqueria"]);

            Sector::where('id', 9)
                        ->update(["name" => "Clinica odontologica"]);

            Sector::where('id', 10)
                        ->update(["name" => "Fruteria"]);

            Sector::where('id', 11)
                        ->update(["name" => "Tiendas de electronica"]);

            Sector::where('id', 12)
                        ->update(["name" => "Tiendas de cosmetica"]);

            Sector::where('id', 13)
                        ->update(["name" => "Farmacia"]);

            Sector::where('id', 14)
                        ->update(["name" => "Ferreteria"]);

            Sector::where('id', 15)
                        ->update(["name" => "Carniceria"]);

            Sector::where('id', 16)
                        ->update(["name" => "Gimnasio"]);

            Sector::where('id', 17)
                        ->update(["name" => "Taller mecanico"]);

            Sector::where('id', 18)
                        ->update(["name" => "Cafeteria"]);

            Sector::where('id', 19)
                        ->update(["name" => "Tintoreria / lavanderia"]);

            Sector::where('id', 20)
                        ->update(["name" => "Inmuebles"]);

            Sector::where('id', 21)
                        ->update(["name" => "Ropa"]);

            Sector::where('id', 22)
                        ->update(["name" => "Tiendas de mascotas"]);

            Sector::where('id', 23)
                        ->update(["name" => "Heladeria"]);

            Sector::where('id', 24)
                        ->update(["name" => "Autolavado"]);

            Sector::where('id', 25)
                        ->update(["name" => "Tiendas dieteticas"]);

            Sector::where('id', 26)
                        ->update(["name" => "Textil"]);

            Sector::where('id', 27)
                        ->update(["name" => "Informatica"]);

            Sector::where('id', 28)
                        ->update(["name" => "Ocio"]);

            Sector::where('id', 29)
                        ->update(["name" => "-"]);

            Sector::where('id', 30)
                        ->update(["name" => "Otro"]);

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "FEB2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Sector::where('id', 5)
                        ->update(["name" => "Salud"]);

            Sector::where('id', 11)
                        ->update(["name" => "Estanco"]);

            Sector::where('id', 12)
                        ->update(["name" => "Locales"]);

            Sector::where('id', 29)
                        ->update(["name" => "Pescaderia"]);


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "MAR2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Service::where('roles_slug', 'cliente.registrado')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'usuario.lite')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'usuario.estandar.mayor')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'usuario.estandar.menor')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'usuario.premium.mayor')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'usuario.premium.menor')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'cliente.fase.evaluacion')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'cliente.fase.analisis')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'cliente.fase.ejecucion')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'cliente.fase.asesoramiento.integral')
                        ->update(["flag_active" => false]);

            Service::where('roles_slug', 'cliente.anual')
                        ->update(["flag_active" => true]);

            Service::where('roles_slug', 'cliente.mensual')
                        ->update(["flag_active" => true]);

            Log::info('----- Desactivando los servicios de planes, a excepción de cliente.anual y cliente.mensual -----');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "MAR2023-2";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            Service::create([
                'name'                          =>  'Video de Negocio - Plan Mensual',
                'slug'                          =>  'video.negocio.plan.mensual',
                'description'                   =>  'Si no puedes acudir a visitar el negocio, porque te encuentras fuera de España, nosotros podemos realizar un video para ti',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Video de Negocio - Plan Mensual',
                'slug_payment'                  =>  'video.negocio.plan.mensual',
                'recommended_price'             =>  '120€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Video de Negocio - Plan Anual',
                'slug'                          =>  'video.negocio.plan.anual',
                'description'                   =>  'Si no puedes acudir a visitar el negocio, porque te encuentras fuera de España, nosotros podemos realizar un video para ti',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Video de Negocio - Plan Anual',
                'slug_payment'                  =>  'video.negocio.plan.anual',
                'recommended_price'             =>  '75€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Acompañamiento Visita - Plan Mensual',
                'slug'                          =>  'acompanamiento.visita.plan.mensual',
                'description'                   =>  'Realizamos un informe de visita analizando los puntos fuertes y débiles de un negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Acompañamiento Visita - Plan Mensual',
                'slug_payment'                  =>  'acompanamiento.visita.plan.mensual',
                'recommended_price'             =>  '90€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Acompañamiento Visita - Plan Anual',
                'slug'                          =>  'acompanamiento.visita.plan.anual',
                'description'                   =>  'Realizamos un informe de visita analizando los puntos fuertes y débiles de un negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Acompañamiento Visita - Plan Anual',
                'slug_payment'                  =>  'acompanamiento.visita.plan.anual',
                'recommended_price'             =>  '50€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Legal - Plan Mensual',
                'slug'                          =>  'analisis.legal.plan.mensual',
                'description'                   =>  'Revisamos los documentos básicos de cara a una adquisición',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Análisis Legal - Plan Mensual',
                'slug_payment'                  =>  'analisis.legal.plan.mensual',
                'recommended_price'             =>  '1250€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Legal - Plan Anual',
                'slug'                          =>  'analisis.legal.plan.anual',
                'description'                   =>  'Revisamos los documentos básicos de cara a una adquisición',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Análisis Legal - Plan Anual',
                'slug_payment'                  =>  'analisis.legal.plan.anual',
                'recommended_price'             =>  '900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero - Plan Mensual',
                'slug'                          =>  'analisis.financiero.plan.mensual',
                'description'                   =>  'Contrastaremos los números que indica el vendedor VS los números que podemos solicitar de forma independiente',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Análisis Financiero - Plan Mensual',
                'slug_payment'                  =>  'analisis.financiero.plan.mensual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Financiero - Plan Anual',
                'slug'                          =>  'analisis.financiero.plan.anual',
                'description'                   =>  'Contrastaremos los números que indica el vendedor VS los números que podemos solicitar de forma independiente',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Análisis Financiero - Plan Anual',
                'slug_payment'                  =>  'analisis.financiero.plan.anual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Legal y Negociación - Plan Mensual',
                'slug'                          =>  'analisis.legal.negociacion.plan.mensual',
                'description'                   =>  'Contactaremos con el vendedor y rebajaremos el precio de la adquisición del traspaso a tu favor',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Análisis Legal y Negociación - Plan Mensual',
                'slug_payment'                  =>  'analisis.legal.negociacion.plan.mensual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Análisis Legal y Negociación - Plan Anual',
                'slug'                          =>  'analisis.legal.negociacion.plan.anual',
                'description'                   =>  'Contactaremos con el vendedor y rebajaremos el precio de la adquisición del traspaso a tu favor',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Análisis Legal y Negociación - Plan Anual',
                'slug_payment'                  =>  'analisis.legal.negociacion.plan.anual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Ejecución de Adquisición - Plan Mensual',
                'slug'                          =>  'ejecucion.adquisicion.plan.mensual',
                'description'                   =>  'El ejecutivo te acompañará en la puesta en marcha y activación del negocio: cambios de titularidades, revisiones finales, etc.',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Ejecución de Adquisición - Plan Mensual',
                'slug_payment'                  =>  'ejecucion.adquisicion.plan.mensual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Ejecución de Adquisición - Plan Anual',
                'slug'                          =>  'ejecucion.adquisicion.plan.anual',
                'description'                   =>  'El ejecutivo te acompañará en la puesta en marcha y activación del negocio: cambios de titularidades, revisiones finales, etc.',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Ejecución de Adquisición - Plan Anual',
                'slug_payment'                  =>  'ejecucion.adquisicion.plan.anual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Creación de Empresa - Plan Mensual',
                'slug'                          =>  'creacion.empresa.plan.mensual',
                'description'                   =>  'Crearemos tu empresa y prepararemos todo de manera que sólo te preocupes del negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Creación de Empresa - Plan Mensual',
                'slug_payment'                  =>  'creacion.empresa.plan.mensual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Creación de Empresa - Plan Anual',
                'slug'                          =>  'creacion.empresa.plan.anual',
                'description'                   =>  'Crearemos tu empresa y prepararemos todo de manera que sólo te preocupes del negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Creación de Empresa - Plan Anual',
                'slug_payment'                  =>  'creacion.empresa.plan.anual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Planificación de Marketing - Plan Mensual',
                'slug'                          =>  'planificacion.marketing.plan.mensual',
                'description'                   =>  'Realizaremos un plan online y offline para el éxito de tu negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Planificación de Marketing - Plan Mensual',
                'slug_payment'                  =>  'planificacion.marketing.plan.mensual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Planificación de Marketing - Plan Anual',
                'slug'                          =>  'planificacion.marketing.plan.anual',
                'description'                   =>  'Realizaremos un plan online y offline para el éxito de tu negocio',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Planificación de Marketing - Plan Anual',
                'slug_payment'                  =>  'planificacion.marketing.plan.anual',
                'recommended_price'             =>  'Monto mínimo 900€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Formación de Personal - Plan Mensual',
                'slug'                          =>  'formacion.personal.plan.mensual',
                'description'                   =>  'Capacitamos a tu equipo en sesiones presenciales multidisciplinares: ventas, atención al cliente, etc.',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Formación de Personal - Plan Mensual',
                'slug_payment'                  =>  'formacion.personal.plan.mensual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Formación de Personal - Plan Anual',
                'slug'                          =>  'formacion.personal.plan.anual',
                'description'                   =>  'Capacitamos a tu equipo en sesiones presenciales multidisciplinares: ventas, atención al cliente, etc.',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Formación de Personal - Plan Anual',
                'slug_payment'                  =>  'formacion.personal.plan.anual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Reunión con Experto - Plan Mensual',
                'slug'                          =>  'reunion.experto.plan.mensual',
                'description'                   =>  'Preparamos una reunión con el experto que necesites para el mejor fin de tu emprendimiento',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Reunión con Experto - Plan Mensual',
                'slug_payment'                  =>  'reunion.experto.plan.mensual',
                'recommended_price'             =>  '90€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Reunión con Experto - Plan Anual',
                'slug'                          =>  'reunion.experto.plan.anual',
                'description'                   =>  'Preparamos una reunión con el experto que necesites para el mejor fin de tu emprendimiento',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Reunión con Experto - Plan Anual',
                'slug_payment'                  =>  'reunion.experto.plan.anual',
                'recommended_price'             =>  '50€',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Contabilidad e Impuestos - Plan Mensual',
                'slug'                          =>  'contabilidad.impuestos.plan.mensual',
                'description'                   =>  'Te apoyamos con tu contabilidad e impuestos',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Contabilidad e Impuestos - Plan Mensual',
                'slug_payment'                  =>  'contabilidad.impuestos.plan.mensual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Contabilidad e Impuestos - Plan Anual',
                'slug'                          =>  'contabilidad.impuestos.plan.anual',
                'description'                   =>  'Te apoyamos con tu contabilidad e impuestos',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Contabilidad e Impuestos - Plan Anual',
                'slug_payment'                  =>  'contabilidad.impuestos.plan.anual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);


            Service::create([
                'name'                          =>  'Reporte de Facturación - Plan Mensual',
                'slug'                          =>  'reporte.facturacion.plan.mensual',
                'description'                   =>  'Obtendrás el detalle de la facturación',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.mensual',
                'name_payment'                  =>  'Reporte de Facturación - Plan Mensual',
                'slug_payment'                  =>  'reporte.facturacion.plan.mensual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Service::create([
                'name'                          =>  'Reporte de Facturación - Plan Anual',
                'slug'                          =>  'reporte.facturacion.plan.anual',
                'description'                   =>  'Obtendrás el detalle de la facturación',
                'type'                          =>  'AddOn',
                'roles_slug'                    =>  'cliente.anual',
                'name_payment'                  =>  'Reporte de Facturación - Plan Anual',
                'slug_payment'                  =>  'reporte.facturacion.plan.anual',
                'recommended_price'             =>  'Solicitar monto al Ejecutivo',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);

            Log::info('----- Creando los AddOn para los planes Mensual y Anual -----');



            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }



        $code = "MAR2023-3";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación Previa de Vencimiento de Plan - Asesor';
            $notificationType->slug = 'notificacion.previa.vencimiento.plan.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = 'Plan de Videoportal de *email_client* *name_client* *surname_client* está por culminar';
            $notificationType->message = 'El *name_plan* del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) finaliza el día *plan_expiration_date*';
            $notificationType->replicate_notification = 'notificacion.previa.vencimiento.plan.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.previa.vencimiento.plan.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación Previa de Vencimiento de Plan - Empleado';
            $notificationType->slug = 'notificacion.previa.vencimiento.plan.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = 'Plan de Videoportal de *email_client* *name_client* *surname_client* está por culminar';
            $notificationType->message = 'El *name_plan* del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) finaliza el día *plan_expiration_date*. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '1,11,12,13,14,15,16,17,18,5,6';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.previa.vencimiento.plan.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación Previa de Vencimiento de Plan - Cliente';
            $notificationType->slug = 'notificacion.previa.vencimiento.plan.cliente';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Su Plan de VideoPortal de Negocios está por culminar';
            $notificationType->message = 'Te recordamos que tu *name_plan* tiene como fecha de vencimiento el día *plan_expiration_date*';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.previa.vencimiento.plan.cliente -----');



            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación de Vencimiento de Plan - Asesor';
            $notificationType->slug = 'notificacion.vencimiento.plan.adviser';
            $notificationType->user_type = 'adviser';
            $notificationType->title = 'Plan de Videoportal de *email_client* *name_client* *surname_client* ha culminado';
            $notificationType->message = 'El *name_plan* del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha culminado.';
            $notificationType->replicate_notification = 'notificacion.vencimiento.plan.employee';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.vencimiento.plan.adviser -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación de Vencimiento de Plan - Empleado';
            $notificationType->slug = 'notificacion.vencimiento.plan.employee';
            $notificationType->user_type = 'employee';
            $notificationType->title = 'Plan de Videoportal de *email_client* *name_client* *surname_client* ha culminado';
            $notificationType->message = 'El *name_plan* del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha culminado. Asesor: *name_employee* *surname_employee*.';
            $notificationType->roles_id = '1,11,12,13,14,15,16,17,18,5,6';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.vencimiento.plan.employee -----');

            $notificationType = new NotificationType();
            $notificationType->name = 'Notificación de Vencimiento de Plan - Cliente';
            $notificationType->slug = 'notificacion.vencimiento.plan.cliente';
            $notificationType->user_type = 'client';
            $notificationType->title = 'Su Plan de VideoPortal de Negocios ha culminado';
            $notificationType->message = 'Su *name_plan* de Videoportal de Negocios culminó.';
            if($notificationType->save())
                Log::info('----- Agregada la notificación con slug: notificacion.vencimiento.plan.cliente -----');


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }




        $code = "JUL2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {
            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alameda',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alcaucín',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alfarnate',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alfarnatejo',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Algarrobo',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Algatocín',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alhaurín de la Torre',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alhaurín el Grande',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Almáchar',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Almargen',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Almogía',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Álora',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alozaina',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Alpandeire',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Antequera',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Árchez',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Archidona',
                'flag_city' => true,
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Ardales',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Arenas',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Arriate',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Atajate',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benadalid',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benahavís',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benalauría',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benalmádena',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benamargosa',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benamocarra',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benaoján',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Benarrabá',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'El Borge',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'El Burgo',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Campillos',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Canillas de Aceituno',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Canillas de Albaida',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cañete la Real',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Carratraca',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cartajima',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cártama',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Casabermeja',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Casarabonela',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Casares',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Coín',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Colmenar',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Comares',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cómpeta',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cortes de la Frontera',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cuevas Bajas',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cuevas de San Marcos',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cuevas del Becerro',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Cútar',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Estepona',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Faraján',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Frigiliana',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Fuengirola',
                'flag_city' => true,
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Fuente de Piedra',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Gaucín',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Genalguacil',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Guaro',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Humilladero',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Igualeja',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Istán',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Iznate',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Jimera de Líbar',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Jubrique',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Júzcar',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Macharaviaya',
            ]);

            $malaga = new Municipality();
            $malaga->province_id = 32;
            $malaga->name = 'Málaga';
            $malaga->flag_city = true;
            $malaga->save();

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Manilva',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Marbella',
                'flag_city' => true,
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Mijas',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Moclinejo',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Mollina',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Monda',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Montecorto',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Montejaque',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Nerja',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Ojén',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Parauta',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Periana',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Pizarra',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Pujerra',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Rincón de la Victoria',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Riogordo',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Ronda',
                'flag_city' => true,
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Salares',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Sayalonga',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Sedella',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Serrato',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Sierra de Yeguas',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Teba',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Tolox',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Torremolinos',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Torrox',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Totalán',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Valle de Abdalajís',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Vélez-Málaga',
                'flag_city' => true,
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Villanueva de Algaidas',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Villanueva de la Concepción',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Villanueva de Tapia',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Villanueva del Rosario',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Villanueva del Trabuco',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Viñuela',
            ]);

            Municipality::create([
                'province_id'  =>  32, // Málaga
                'name'  =>  'Yunquera',
            ]);

            // Actualizando municipality_id de los negocios de Málaga
            Business::whereIn('municipality_id', [211, 230])
                    ->update(['municipality_id' => $malaga->id]);

            // Eliminando municipios antiguos de Málaga
            $aux = Municipality::find(211);
            $aux->delete();

            $aux = Municipality::find(230);
            $aux->delete();

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "AGO2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $role = config('roles.models.role')::create([
                'name' => 'Cliente Free Trial',
                'slug' => 'cliente.free.trial',
                'description' => 'Son aquellos usuarios que poseen una suscripción de prueba de VideoPortal por un tiempo limitado',
                'level' => 2,
            ]);
            if($role->save())
                Log::info("-----Creado Rol Cliente Free Trial-----");


            Service::create([
                'name'                          =>  'Plan Free Trial',
                'slug'                          =>  'plan.free.trial',
                'type'                          =>  'Plan',
                'roles_slug'                    =>  'cliente.free.trial',
                'name_payment'                  =>  'Pago Plan Free Trial',
                'slug_payment'                  =>  'pago.plan.free.trial',
                'recommended_price'             =>  'Gratis',
                'flag_recurring_payment'        =>  false,
                'flag_payment_in_installments'  =>  false
            ]);
            Log::info("-----Creado Plan Free Trial-----");


            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Free Trial - Asesor',
                'slug'      =>  'solicitud.cliente.plan.free.trial.adviser',
                'user_type' =>  'adviser',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el Plan Free Trial',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el Plan Free Trial.',
                'replicate_notification'    =>  'solicitud.cliente.plan.free.trial.employee'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.anual.adviser -----');

            NotificationType::create([
                'name'      =>  'Solicitud de un cliente a Plan Free Trial - Empleado',
                'slug'      =>  'solicitud.cliente.plan.free.trial.employee',
                'user_type' =>  'employee',
                'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el Plan Free Trial a su asesor',
                'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el Plan Free Trial. Asesor: *name_employee* *surname_employee*.',
                'roles_id'  =>  '11,12,13'
            ]);
            Log::info('----- Agregada la notificación con slug: solicitud.cliente.plan.free.trial.employee -----');

            NotificationType::create([
                'name'      =>  'Cambio a Plan Free Trial',
                'slug'      =>  'cambio.plan.free.trial',
                'user_type' =>  'client',
                'title'     =>  'Ahora tienes un nuevo plan',
                'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el Plan Free Trial.'
            ]);
            Log::info('----- Agregada la notificación con slug: cambio.plan.free.trial -----');



            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "OCT2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            foreach (ClientTimeline::all() as $key => $clientTimeline) {

                switch($clientTimeline->module)
                {
                    case 'Autenticación':           $clientTimeline->module_eng = 'Auth';
                                                    break;

                    case 'Servicio de Cliente':     $clientTimeline->module_eng = 'AddedService';
                                                    break;

                    case 'Asesor Asignado':         $clientTimeline->module_eng = 'Assigned Advisor';
                                                    break;

                    case 'Cliente':                 $clientTimeline->module_eng = 'Client';
                                                    break;

                    case 'Petición del Cliente':    $clientTimeline->module_eng = 'Client Request';
                                                    break;

                    case 'Envío de Email':          $clientTimeline->module_eng = 'Email Management';
                                                    break;

                    case 'Análisis Financiero':     $clientTimeline->module_eng = 'Financial Analysis';
                                                    break;

                    case 'Pago':                    $clientTimeline->module_eng = 'Payment';
                                                    break;

                    case 'Servicio':                $clientTimeline->module_eng = 'Service';
                                                    break;

                    case 'Videollamada':            $clientTimeline->module_eng = 'VideoCall';
                                                    break;

                    default: $clientTimeline->module_eng = $clientTimeline->module;
                }

                switch($clientTimeline->type_crud) {

                    case 'creado':          $clientTimeline->type_crud_eng = 'create';
                                            break;

                    case 'actualizado':     $clientTimeline->type_crud_eng = 'update';
                                            break;

                    case 'borrado':         $clientTimeline->type_crud_eng = 'delete';
                                            break;

                    case 'limpiado':        $clientTimeline->type_crud_eng = 'cleaned';
                                            break;

                    case 'asociado':        $clientTimeline->type_crud_eng = 'associate';
                                            break;

                    case 'aprobado':        $clientTimeline->type_crud_eng = 'approve';
                                            break;

                    case 'rechazado':       $clientTimeline->type_crud_eng = 'rejected';
                                            break;

                    case 'enviado':         $clientTimeline->type_crud_eng = 'send';
                                            break;

                    default:                $clientTimeline->type_crud_eng = $clientTimeline->type_crud;
                }

                $clientTimeline->save();

            }


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }

        $code = "OCT2023-2";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $autoCommunity = new AutonomousCommunity();
            $autoCommunity->name = 'Franquicia';
            $autoCommunity->save();

            $province = new Province();
            $province->name = 'Franquicia';
            $province->autonomous_community_id = $autoCommunity->id;
            $province->save();

            $municipality = new Municipality();
            $municipality->name = 'Franquicia';
            $municipality->flag_city = 0;
            $municipality->province_id = $province->id;
            $municipality->save();

            Log::info('---- Creando Comunidad Autónoma - Provincia - Municipio para Franquicia ----');

            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        $code = "DIC2023-1";

        if(UpdateData::where('code', $code)->count() == 0)
        {

            $municipalityUDService = new MunicipalityUDService();
            $municipalityUDService->madridProvince();


            $updateData = new UpdateData();
            $updateData->code = $code;
            $updateData->save();
        }


        return response()->json(['status'   =>  'success'], 200);
    }
}
