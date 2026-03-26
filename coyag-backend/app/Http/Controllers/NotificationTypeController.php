<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use jeremykenedy\LaravelRoles\Models\Role;

class NotificationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notificationTypes = NotificationType::select('notification_types.id as id_notification_type', 'notification_types.name as name_notification_type', 'notification_types.slug as slug_notification_type', 'notification_types.user_type as user_type_notification_type', 'notification_types.title as title_notification_type', 'notification_types.message as message_notification_type', 'notification_types.active as active_notification_type', 'notification_types.roles_id as roles_id_notification_type', 'notification_types.replicate_notification as replicate_notification_type', 'notification_types.created_at as created_at_notification_type', 'notification_types.updated_at as updated_at_notification_type')
                                    ->get();

        return response()->json([
            'status'        =>  'success',
            'notificationTypes' => $notificationTypes
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
        $validator = Validator::make($request->all(), [
            'name'          =>  'required|max:128|unique:notification_types',
            'slug'          =>  'required|max:64|unique:notification_types',
            'user_type'     =>  'required|in:"adviser", "client", "employee"',
            'title'         =>  'required|max:192',
            'message'       =>  'required|max:255',
            'active'        =>  'required|numeric|min:0|max:1'
        ],
        [
            'name.required'         =>  'El Nombre es Requerido',
            'name.max'              =>  'El Nombre no debe pasar de 128 caracteres',
            'name.unique'           =>  'El Nombre ya está siendo utilizado',
            'slug.required'         =>  'El Slug es Requerido',
            'slug.max'              =>  'El Slug no debe pasar de 64 caracteres',
            'slug.unique'           =>  'El Slug ya está siendo utilizado',
            'user_type.required'    =>  'El Tipo de Usuario es Requerido',
            'user_type.in'          =>  'El Tipo de Usuario debe ser: adviser, client o employee',
            'title.required'        =>  'El Título es requerido',
            'title.max'             =>  'El Título no debe pasar de 192 caracteres',
            'message.required'      =>  'El Mensaje es requerido',
            'message.max'           =>  'El Mensaje no debe pasar de 255 caracteres',
            'active.required'       =>  'Active es requerido',
            'active.numeric'        =>  'Active debe ser 1 para true o 0 para false',
            'active.min'            =>  'Active no debe ser menor a 0',
            'active.max'            =>  'Active no debe ser mayor a 0'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $notificationType = new NotificationType();
        $notificationType->name = $request->name;
        $notificationType->slug = $request->slug;
        $notificationType->user_type = $request->user_type;
        $notificationType->title = $request->title;
        $notificationType->message = $request->message;
        $notificationType->active = $request->active;

        if($request->user_type == 'employee')
        {
            $resp = $this->checkRolesId($request);

            if($resp != '')
                return $resp;

            $notificationType->roles_id = $request->roles_id;
        }



        if($request->exists('replicate_notification'))
        {
            if($request->user_type == 'adviser')
            {
                $resp = $this->checkSlugNotification($request);

                if($resp != '')
                    return $resp;

                $notificationType->replicate_notification = $request->replicate_notification;
            }
            else
            {
                return response()->json(['errors' => 'El slug de replicación (replication_notification) no debe existir si el tipo de usuario de la notificación es client o employee'], 422);
            }

        }


        if($notificationType->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el Tipo de Notificación'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idNotificationType
     * @return \Illuminate\Http\Response
     */
    public function show($idNotificationType)
    {
        if(NotificationType::where('id', $idNotificationType)->count() > 0)
        {
            $notificationType = NotificationType::select('notification_types.id as id_notification_type', 'notification_types.name as name_notification_type', 'notification_types.slug as slug_notification_type', 'notification_types.user_type as user_type_notification_type', 'notification_types.title as title_notification_type', 'notification_types.message as message_notification_type', 'notification_types.active as active_notification_type', 'notification_types.roles_id as roles_id_notification_type', 'notification_types.replicate_notification as replicate_notification_type', 'notification_types.created_at as created_at_notification_type', 'notification_types.updated_at as updated_at_notification_type')
                                        ->where('id', $idNotificationType)
                                        ->first();

            return response()->json([
                'status'    => 'success',
                'notificationType'  =>  $notificationType
            ], 200);
        }

        return response()->json(['errors' => 'No existe el Tipo de Notificación'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idNotificationType)
    {
        if(NotificationType::where('id', $idNotificationType)->count() > 0)
        {
            $notificationType = NotificationType::find($idNotificationType);

            if($request->exists('name'))
            {
                if(NotificationType::where('name', $request->name)->count() > 0)
                    return response()->json(['errors' => 'El Nombre ya está siendo utilizado'], 422);

                if(strlen($request->name) > 128)
                    return response()->json(['errors' => 'El Nombre no debe pasar de 128 caracteres'], 422);

                $notificationType->name = $request->name;
            }


            if($request->exists('slug'))
            {
                if(NotificationType::where('slug', $request->slug)->count() > 0)
                    return response()->json(['errors' => 'El Slug ya está siendo utilizado'], 422);

                if(strlen($request->slug) > 64)
                    return response()->json(['errors' => 'El Slug no debe pasar de 64 caracteres'], 422);

                $notificationType->slug = $request->slug;
            }


            if($request->exists('user_type'))
            {
                if($request->user_type != 'adviser' && $request->user_type != 'client' &&  $request->user_type != 'employee')
                    return response()->json(['errors' => 'El Tipo de Usuario debe ser: adviser, client o employee'], 422);

                if($request->user_type == 'adviser' || $request->user_type == 'client')
                    $notificationType->roles_id = null;

                if($request->user_type == 'employee' || $request->user_type == 'client')
                    $notificationType->replicate_notification = null;

                $notificationType->user_type = $request->user_type;
            }

            if($request->exists('title'))
            {
                if(strlen($request->title) > 192)
                    return response()->json(['errors' => 'El Título no debe pasar de 192 caracteres'], 422);

                $notificationType->title = $request->title;
            }

            if($request->exists('message'))
            {
                if(strlen($request->message) > 255)
                    return response()->json(['errors' => 'El Mensaje no debe pasar de 255 caracteres'], 422);

                $notificationType->message = $request->message;
            }

            if($request->exists('active'))
            {
                if($request->active != 0 &&  $request->active != 1)
                    return response()->json(['errors' => 'Active debe ser 0 o 1'], 422);
                $notificationType->active = $request->active;
            }

            if($notificationType->user_type == 'employee')
            {
                if($request->exists('roles_id'))
                {
                    $resp = $this->checkRolesId($request);

                    if($resp != '')
                        return $resp;

                    $notificationType->roles_id = $request->roles_id;
                }

                if($notificationType->roles_id == '' || $notificationType->roles_id == null)
                    return response()->json(['errors' => 'El campo roles_id no puede estar vacío o nulo'], 422);

            }

            if($request->exists('replicate_notification'))
            {
                if($notificationType->user_type != 'adviser')
                    return response()->json(['errors' => 'El slug de replicación (replication_notification) no debe existir si el tipo de usuario de la notificación es client o employee'], 422);

                $resp = $this->checkSlugNotification($request);

                if($resp != '')
                    return $resp;

                $notificationType->replicate_notification = $request->replicate_notification;
            }


            if($notificationType->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Tipo de Notificación'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Notificación'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idNotificationType)
    {

        if(NotificationType::where('id', $idNotificationType)->count() > 0)
        {
            $notificationType = NotificationType::find($idNotificationType);

            if(Notification::where('notification_type_id', $idNotificationType)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Tipo de Notificación porque tiene Notificaciones Asociadas'], 422);

            if($notificationType->delete()) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo borrar el Tipo de Notificación'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Notificación'], 422);

    }

    private function checkRolesId(Request $request)
    {
        /**
         * Método que comprueba que todos los IDs que se encuentran en el String $roles_id sean IDs válidos de empleado
         */

        if(!$request->exists('roles_id'))
            return response()->json(['errors' => 'Debe enviar roles_id porque user_type es employee'], 422);

        $rolesId = str_replace(' ', '', $request->roles_id);

        if($rolesId == "")
            return response()->json(['errors' => 'El campo roles_id no puede estar vacío'], 422);

        $arrAuxRoleId = explode(',', $rolesId);

        for($i = 0; $i < count($arrAuxRoleId); $i++)
        {
            $role = Role::where('id', $arrAuxRoleId[$i])->first();

            if($role)
            {
                if($role->slug == 'usuario.premium.mayor' || $role->slug == 'usuario.premium.menor' || $role->slug == 'usuario.estandar.mayor' || $role->slug == 'usuario.estandar.menor' || $role->slug == 'usuario.lite' || $role->slug == 'cliente.registrado' || $role->slug == 'usuario.free')
                    return response()->json(['errors' => "El role_id $arrAuxRoleId[$i] no es válido. El Rol debe existir y pertenecer a un empleado"], 422);
            }
            else
                return response()->json(['errors' => "El role_id $arrAuxRoleId[$i] no es válido. El Rol debe existir y pertenecer a un empleado"], 422);
        }

        return '';
    }

    private function checkSlugNotification(Request $request)
    {
        /**
         * Método que comprueba que el slug exista
         */

        $notificationType = NotificationType::where('slug', $request->replicate_notification)
                                                ->where('user_type', 'employee')
                                                ->count();

        if($notificationType == 0)
            return response()->json(['errors' => "El slug de replicación (replicate_notification) NO existe"], 422);
        else
            return '';
    }
}
