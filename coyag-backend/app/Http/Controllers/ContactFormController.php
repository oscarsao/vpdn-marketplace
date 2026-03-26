<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Models\Service;
use App\Notifications\ContactFormNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Notification;
use \stdClass;

class ContactFormController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $role = getRoleOfClientIndicatingTheTypeOfService(Auth::user()->client, 'Plan');
        if($role->slug == 'cliente.registrado')
            return response()->json(['errors' => ['Error en Plan' => ['El Cliente no tiene un plan de VideoPortal asociado']]], 403);

        if($role->slug == 'cliente.free.trial')
            return response()->json(['errors' => ['Error en Plan' => ['Contacte un asesor para mejorar su plan']]], 403);

        $object = new stdClass();

        $validator = Validator::make($request->all(), [
            'names'     =>  'max:128',
            'surnames'  =>  'max:128',
            'phone'     =>  'max:128',
            'email'     =>  'max:128|email',
            'reason'    =>  'required|in:   "Video de negocio",
                                            "Acompañamiento Visita",
                                            "Abogado - Análisis Documentos",
                                            "Abogado - Análisis Docs + Negociación",
                                            "Estudio Financiero",
                                            "Planificación de Marketing",
                                            "Ejecución de Adquisición",
                                            "Creación de Empresa",
                                            "Formación de Personal",
                                            "Reunión con Experto",
                                            "Contabilidad e Impuestos",
                                            "Reporte de Facturación"',
        ],
        [
            'names.max'         => 'Los Nombres no pueden exceder los 128 caracteres',
            'surnames.max'      => 'Los Apellidos no pueden exceder los 128 caracteres',
            'phone.max'         => 'El Teléfono Celular no puede exceder los 128 caracteres',
            'email.max'         => 'El Correo Electrónico no puede exceder los 128 caracteres',
            'email.email'       => 'El Correo Electrónico es inválido',
            'reason.required'   => 'El Motivo de Consulta es requerido',
            'reason.in'         => 'El Motivo de Consulta es inválido ',
            'business.required' => 'El Negocio es requerido',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        if($request->exists('names')) $object->names = $request->names;
        else $object->names = Auth::user()->client->names;

        if($request->exists('surnames')) $object->surnames = $request->surnames;
        else $object->surnames = Auth::user()->client->surnames;

        if($request->exists('phone')) $object->phone = $request->phone;
        else $object->phone = Auth::user()->client->phone_mobile;

        if($request->exists('email')) $object->email = $request->email;
        else $object->email = Auth::user()->email;

        $object->reason = $request->reason;
        $object->business = $request->business;

        // TODO: Esta ruta debe ser relativa
        $object->link = 'https://videoportaldenegocios.es/videoportal/#/admin/cliente/' . Auth::user()->client->id;


        try {
            Notification::route('mail', 'info@videoportaldenegocios.es')
                        ->notify(new ContactFormNotification($object));

            $this->createClientRequest($request->reason, $role);

            addClientTimeline(Auth::user()->client->id, 1, 'Client Request', 'create', true);

            return response()->json(['status' => 'success'], 200);
        }
        catch (Exception $e) {
            Log::error("Error - COYAG -> Excepción capturada en ContactFormController: $e");
        }

        return response()->json(['error' => 'No se pudo enviar la Notificación'], 422);
    }

    private function createClientRequest($reason, $role)
    {
        $slugService;

        if($role->slug == 'cliente.anual')
        {
            if($reason == 'Video de negocio') $slugService = 'video.negocio.plan.anual';

            if($reason == 'Acompañamiento Visita') $slugService = 'acompanamiento.visita.plan.anual';

            if($reason == 'Abogado - Análisis Documentos') $slugService = 'analisis.legal.plan.anual';

            if($reason == 'Abogado - Análisis Docs + Negociación') $slugService = 'analisis.legal.negociacion.plan.anual';

            if($reason == 'Estudio Financiero') $slugService = 'analisis.financiero.plan.anual';

            if($reason == 'Planificación de Marketing') $slugService = 'planificacion.marketing.plan.anual';

            if($reason == 'Ejecución de Adquisición') $slugService = 'ejecucion.adquisicion.plan.anual';

            if($reason == 'Creación de Empresa') $slugService = 'creacion.empresa.plan.anual';

            if($reason == 'Formación de Personal') $slugService = 'formacion.personal.plan.anual';

            if($reason == 'Reunión con Experto') $slugService = 'reunion.experto.plan.anual';

            if($reason == 'Contabilidad e Impuestos') $slugService = 'contabilidad.impuestos.plan.anual';

            if($reason == 'Reporte de Facturación') $slugService = 'reporte.facturacion.plan.anual';

        }

        if($role->slug == 'cliente.mensual')
        {
            if($reason == 'Video de negocio') $slugService = 'video.negocio.plan.mensual';

            if($reason == 'Acompañamiento Visita') $slugService = 'acompanamiento.visita.plan.mensual';

            if($reason == 'Abogado - Análisis Documentos') $slugService = 'analisis.legal.plan.mensual';

            if($reason == 'Abogado - Análisis Docs + Negociación') $slugService = 'analisis.legal.negociacion.plan.mensual';

            if($reason == 'Estudio Financiero') $slugService = 'analisis.financiero.plan.mensual';

            if($reason == 'Planificación de Marketing') $slugService = 'planificacion.marketing.plan.mensual';

            if($reason == 'Ejecución de Adquisición') $slugService = 'ejecucion.adquisicion.plan.mensual';

            if($reason == 'Creación de Empresa') $slugService = 'creacion.empresa.plan.mensual';

            if($reason == 'Formación de Personal') $slugService = 'formacion.personal.plan.mensual';

            if($reason == 'Reunión con Experto') $slugService = 'reunion.experto.plan.mensual';

            if($reason == 'Contabilidad e Impuestos') $slugService = 'contabilidad.impuestos.plan.mensual';

            if($reason == 'Reporte de Facturación') $slugService = 'reporte.facturacion.plan.mensual';
        }

        if(empty($slugService))
            return;

        $service = Service::where('slug', $slugService)->first();

        $clientRequest = new ClientRequest();
        $clientRequest->client_id = Auth::user()->client->id;
        $clientRequest->service_id = $service->id;
        $clientRequest->save();

    }
}
