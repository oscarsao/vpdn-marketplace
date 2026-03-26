<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = Notification::leftJoin('notification_types', 'notification_types.id', '=', 'notifications.notification_type_id')
                                        ->select('notifications.id as id_notification', 'notifications.employee_id as id_employee', 'notifications.client_id as id_client',  'notifications.title as title_notification', 'notifications.message as message_notification', 'notifications.flag_read as flag_read_notification', 'notifications.url as url_notification', 'notifications.created_at as created_at_notification', 'notifications.updated_at as updated_at_notification', 'notification_types.id as id_notification_type', 'notification_types.name as name_notification_type', 'notification_types.user_type as user_type_notification_type');

        if(isset(Auth::user()->client->id))
        {
            $notifications = $notifications->where('notifications.client_id', '=', Auth::user()->client->id)
                                            ->where('notification_types.user_type', 'client');
        }
        else
        {
            $notifications = $notifications->where('notifications.employee_id', '=', Auth::user()->employee->id)
                                            ->where('notification_types.user_type', 'employee');
        }

        $notifications = $notifications->get();

        return response()->json(
            [
                'status' => 'success',
                'notifications' => $notifications
            ], 200);

    }

    public function show($idNotification)
    {

        $notification = Notification::leftJoin('notification_types', 'notification_types.id', '=', 'notifications.notification_type_id')
                                        ->where('notifications.id', '=', $idNotification)
                                        ->select('notifications.id as id_notification', 'notifications.employee_id as id_employee', 'notifications.client_id as id_client',  'notifications.title as title_notification', 'notifications.message as message_notification', 'notifications.flag_read as flag_read_notification', 'notifications.url as url_notification', 'notifications.created_at as created_at_notification', 'notifications.updated_at as updated_at_notification', 'notification_types.id as id_notification_type', 'notification_types.name as name_notification_type', 'notification_types.user_type as user_type_notification_type');

        if(isset(Auth::user()->client->id))
        {
            $notification = $notification->where('notifications.client_id', '=', Auth::user()->client->id)
                                            ->where('notification_types.user_type', 'client');
        }
        else
        {
            $notification = $notification->where('notifications.employee_id', '=', Auth::user()->employee->id)
                                            ->where('notification_types.user_type', 'employee');
        }

        $notification = $notification->first();

        return response()->json(
            [
                'status' => 'success',
                'notification' => $notification
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function changeFlagRead(Request $request, $idNotification)
    {

        $notification = Notification::find($idNotification);

        if($notification)
        {
            $notificationType = NotificationType::where('id', $notification->notification_type_id)->first();

            if(isset(Auth::user()->client->id))
            {
                if($notificationType->user_type != 'client')
                    return response()->json(['errors' => 'No existe la notificación asignada al usuario'], 422);

                if($notification->client_id != Auth::user()->client->id)
                    return response()->json(['errors' => 'No existe la notificación asignada al usuario'], 422);
            }
            else
            {
                if($notificationType->user_type != 'employee')
                    return response()->json(['errors' => 'No existe la notificación asignada al usuario'], 422);

                if($notification->employee_id != Auth::user()->employee->id)
                    return response()->json(['errors' => 'No existe la notificación asignada al usuario'], 422);
            }


            switch($request->flag_read)
            {
                case 0: $notification->flag_read = false; break;
                case 1: $notification->flag_read = true;  break;
                default: return response()->json(['errors' => 'El valor de flag_read es Inválido'], 422);
            }

            if($notification->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el flag read'], 422);
        }

        return response()->json(['errors' => 'No existe la notificación asignada al usuario'], 422);
    }
}
