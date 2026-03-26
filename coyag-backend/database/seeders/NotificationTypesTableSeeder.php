<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotificationType::create([
            'name'      =>  'Registro de Cliente',
            'slug'      =>  'registro.cliente',
            'user_type' =>  'employee',
            'title'     =>  'Nuevo cliente *email_client*',
            'message'   =>  'Se ha registrado el cliente *email_client* (ID: *id_client*).',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,1,5,6'
        ]);

        // Esta notificación le llega al asesor

        NotificationType::create([
            'name'      =>  'Asignación de Asesor - Asesor',
            'slug'      =>  'asignacion.asesor.adviser',
            'user_type' =>  'adviser',
            'title'     =>  'Eres el nuevo asesor de *email_client* *name_client* *surname_client*',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se te ha asignado para que seas su Asesor. Esta asignación fue hecha por *name_generator_employee* *surname_generator_employee*.',
            'replicate_notification'  =>  'asignacion.asesor.employee'

        ]);

        NotificationType::create([
            'name'      =>  'Asignación de Asesor - Empleado',
            'slug'      =>  'asignacion.asesor.employee',
            'user_type' =>  'employee',
            'title'     =>  '*name_employee* *surname_employee* es el nuevo asesor de *email_client* *name_client* *surname_client*',
            'message'   =>  '*name_employee* *surname_employee* es el nuevo Asesor del cliente *email_client* *name_client* *surname_client* (ID: *id_client*). Esta asignación fue hecha por *name_generator_employee* *surname_generator_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Asignación de Asesor - Cliente',
            'slug'      =>  'asignacion.asesor.client',
            'user_type' =>  'client',
            'title'     =>  'Se te ha asignado un nuevo Asesor Comercial',
            'message'   =>  '*name_employee* *surname_employee* es tu nuevo asesor. Recuerda que puedes comunicarte con tu asesor si tienes algún comentario y/o duda.'
        ]);

        // Estas notificaciones (Employee) le llega al asesor y a Directores: Comercial y Ejecutivo
        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Lite - Asesor',
            'slug'      =>  'solicitud.cliente.plan.lite.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Lite',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Lite.',
            'replicate_notification'  =>  'solicitud.cliente.plan.lite.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Lite - Empleado',
            'slug'      =>  'solicitud.cliente.plan.lite.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Lite a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Lite. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Plan Lite',
            'slug'      =>  'cambio.plan.lite',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo Plan',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el Plan Lite.'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Standard - Asesor',
            'slug'      =>  'solicitud.cliente.plan.standard.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Estándar',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Estándar.',
            'replicate_notification'  => 'solicitud.cliente.plan.standard.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Standard - Empleado',
            'slug'      =>  'solicitud.cliente.plan.standard.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Estándar a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Estándar. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Plan Standard',
            'slug'      =>  'cambio.plan.standard',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo Plan',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el Plan Estándar.'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Premium - Asesor',
            'slug'      =>  'solicitud.cliente.plan.premium.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Premium',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Premium.',
            'replicate_notification'  => 'solicitud.cliente.plan.premium.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Premium - Empleado',
            'slug'      =>  'solicitud.cliente.plan.premium.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Premium a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Premium. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Plan Premium',
            'slug'      =>  'cambio.plan.premium',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo Plan',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes el Plan Premium.'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Registrado - Asesor',
            'slug'      =>  'solicitud.cliente.plan.registrado.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Registrado',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Registrado.',
            'replicate_notification'  => 'solicitud.cliente.plan.registrado.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Plan Registrado - Empleado',
            'slug'      =>  'solicitud.cliente.plan.registrado.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado el plan Registrado a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el plan Registrado. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Plan Registrado',
            'slug'      =>  'cambio.plan.registrado',
            'user_type' =>  'client',
            'title'     =>  'No tienes un Plan',
            'message'   =>  'En estos momentos no tienes un Plan asignado. Te recomendamos visitar la sección de Planes y solicitar alguno.'
        ]);

        // Estas notificaciones (Employee) le llega al asesor y a Directores: Comercial y Ejecutivo
        NotificationType::create([
            'name'      =>  'Solicitud de Análisis Financiero Plan Lite - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.plan.lite.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero (Estudio Ciego) del Plan Lite.',
            'replicate_notification'  => 'solicitud.analisis.financiero.plan.lite.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de Análisis Financiero Plan Lite - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.plan.lite.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero (Estudio Ciego) del Plan Lite. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 1 Análisis Financiero Plan Standard - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack1.plan.standard.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Estándar.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack1.plan.standard.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 1 Análisis Financiero Plan Standard - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack1.plan.standard.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Estándar. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 3 Análisis Financieros Plan Standard - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack3.plan.standard.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Estándar.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack3.plan.standard.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 3 Análisis Financieros Plan Standard - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack3.plan.standard.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Estándar. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 5 Análisis Financieros Plan Standard - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack5.plan.standard.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Estándar.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack5.plan.standard.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 5 Análisis Financieros Plan Standard - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack5.plan.standard.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Estándar. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 1 Análisis Financiero Plan Premium - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack1.plan.premium.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Premium.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack1.plan.premium.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 1 Análisis Financiero Plan Premium - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack1.plan.premium.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 1 (un) Análisis Financiero Plan Premium. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 3 Análisis Financieros Plan Premium - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack3.plan.premium.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Premium.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack3.plan.premium.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 3 Análisis Financieros Plan Premium - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack3.plan.premium.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 3 (tres) Análisis Financieros Plan Premium. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 5 Análisis Financieros Plan Premium - Asesor',
            'slug'      =>  'solicitud.analisis.financiero.pack5.plan.premium.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Premium.',
            'replicate_notification'  => 'solicitud.analisis.financiero.pack5.plan.premium.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Pack 5 Análisis Financieros Plan Premium - Empleado',
            'slug'      =>  'solicitud.analisis.financiero.pack5.plan.premium.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado Análisis Financiero a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado el servicio de 5 (cinco) Análisis Financiero Plan Premium. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Análisis Financieros otorgado - Copia Asesor',
            'slug'      =>  'analisis.financiero.otorgado.adviser',
            'user_type' =>  'adviser',
            'title'     =>  'Al cliente *email_client* *name_client* *surname_client* se le ha otorgado Análisis Financieros',
            'message'   =>  'Al cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se le ha otorgado *number_financial_analysis* Análisis Financieros.',
            'replicate_notification'  => 'analisis.financiero.otorgado.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Análisis Financieros otorgado - Copia Empleado',
            'slug'      =>  'analisis.financiero.otorgado.employee',
            'user_type' =>  'employee',
            'title'     =>  'Al cliente *email_client* *name_client* *surname_client* se le ha otorgado Análisis Financieros',
            'message'   =>  'Al cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se le ha otorgado *number_financial_analysis* Análisis Financieros. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '1,2,4,5,11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Análisis Financieros otorgado - Copia Cliente',
            'slug'      =>  'analisis.financiero.otorgado.client',
            'user_type' =>  'client',
            'title'     =>  'Tiene Análisis Financieros disponibles',
            'message'   =>  'Ahora tiene *number_financial_analysis* Análisis Financieros.'
        ]);

        // Carga de pagos pendiente por ser aprobado o rechazado
        NotificationType::create([
            'name'      =>  'Pago cargado - Copia Asesor',
            'slug'      =>  'pago.cargado.adviser',
            'user_type' =>  'adviser',
            'title'     =>  'El pago de *email_client* *name_client* *surname_client* se ha cargado',
            'message'   =>  'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date*, observación: *payment_observation*.',
            'replicate_notification'  => 'pago.cargado.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Pago cargado - Copia Empleado',
            'slug'      =>  'pago.cargado.employee',
            'user_type' =>  'employee',
            'title'     =>  'El pago de *email_client* *name_client* *surname_client* se ha cargado',
            'message'   =>  'El pago del cliente *email_client* *name_client* *surname_client* (ID: *id_client*) se ha cargado el *payment_date*, observación: *payment_observation*. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '1,2,4,5,11,12,13'
        ]);

        NotificationType::create([
            'name'      =>  'Pago cargado - Copia Cliente',
            'slug'      =>  'pago.cargado.client',
            'user_type' =>  'client',
            'title'     =>  'Su pago se ha cargado',
            'message'   =>  'Se ha cargado un pago el *payment_date*.'
        ]);

        // Esta notificación (Employee) le llega al asesor y a Directores: Comercial y Ejecutivo
        NotificationType::create([
            'name'      =>  'Solicitud Videollamada por parte de un Cliente - Asesor',
            'slug'      =>  'solicitud.videollamada.cliente.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado una Videollamada',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado una Videollamada del tipo *video_call_type* sobre el (los) negocio(s) con Código(s): *business_id*.',
            'replicate_notification'  => 'solicitud.videollamada.cliente.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud Videollamada por parte de un Cliente - Empleado',
            'slug'      =>  'solicitud.videollamada.cliente.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado una Videollamada a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado una Videollamada del tipo *video_call_type* sobre el (los) negocio(s) con Código(s): *business_id*. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13'
        ]);

        // Mensajes al cliente
        NotificationType::create([
            'name'      =>  'Recordatorio al cliente para validar su email',
            'slug'      =>  'recordatorio.validar.email.cliente',
            'user_type' =>  'client',
            'title'     =>  'Recuerde validar su email (correo electrónico)',
            'message'   =>  'En estos momentos su email (correo electrónico) NO se encuentra validado, por favor proceda a validarlo.'
        ]);

        NotificationType::create([
            'name'      =>  'Recordatorio al cliente para completar sus datos de perfil',
            'slug'      =>  'recordatorio.completar.datos.perfil',
            'user_type' =>  'client',
            'title'     =>  'Recuerde completar los datos de su perfil',
            'message'   =>  'Hay datos de su perfil que aún no han sido completados (rellenados), por favor proceda a realizarlo.'
        ]);


        // Notificaciones de nuevos planes (Fases)

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


        //------------Notificaciones de Extranjeríá--------------

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Primera Residencia - Asesor',
            'slug'      =>  'solicitud.cliente.extranjeria.primera.res.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Primera Residencia',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Primera Residencia.',
            'replicate_notification'  => 'solicitud.cliente.extranjeria.primera.res.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Primera Residencia - Empleado',
            'slug'      =>  'solicitud.cliente.extranjeria.primera.res.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Primera Residencia a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Primera Residencia. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Extranjería Primera Residencia',
            'slug'      =>  'cambio.extranjeria.primera.res',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo servicio de extranjería',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Primera Residencia.'
        ]);



        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Renovación Primera Residencia - Asesor',
            'slug'      =>  'solicitud.cliente.extranjeria.renovacion.primera.res.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Renovación Primera Residencia',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Renovación Primera Residencia.',
            'replicate_notification'  => 'solicitud.cliente.extranjeria.renovacion.primera.res.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Renovación Primera Residencia - Empleado',
            'slug'      =>  'solicitud.cliente.extranjeria.renovacion.primera.res.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Renovación Primera Residencia',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Renovación Primera Residencia. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Extranjería Renovación Primera Residencia',
            'slug'      =>  'cambio.extranjeria.renovacion.primera.res',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo servicio de extranjería',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Renovación Primera Residencia.'
        ]);



        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Ciudadanía - Asesor',
            'slug'      =>  'solicitud.cliente.extranjeria.ciudadania.adviser',
            'user_type' =>  'adviser',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Ciudadanía',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Ciudadanía.',
            'replicate_notification'  => 'solicitud.cliente.extranjeria.ciudadania.employee'
        ]);

        NotificationType::create([
            'name'      =>  'Solicitud de un cliente a Extranjería Ciudadanía - Empleado',
            'slug'      =>  'solicitud.cliente.extranjeria.ciudadania.employee',
            'user_type' =>  'employee',
            'title'     =>  '*email_client* *name_client* *surname_client* ha solicitado extranjería Ciudadanía a su asesor',
            'message'   =>  'El cliente *email_client* *name_client* *surname_client* (ID: *id_client*) ha solicitado extranjería Ciudadanía. Asesor: *name_employee* *surname_employee*.',
            'roles_id'  =>  '11,12,13,14,15,16,17,18,5,6'
        ]);

        NotificationType::create([
            'name'      =>  'Cambio a Extranjería Ciudadanía',
            'slug'      =>  'cambio.plan.extranjeria.ciudadania',
            'user_type' =>  'client',
            'title'     =>  'Ahora tienes un nuevo servicio de extranjería',
            'message'   =>  'De acuerdo a la Petición que realizaste, ahora tienes Extranjería Ciudadanía.'
        ]);

    }
}
