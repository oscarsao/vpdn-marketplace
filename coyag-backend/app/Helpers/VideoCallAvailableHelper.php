<?php

use App\Models\Client;
use App\Models\VideoCall;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function videoCallsAvailable($videoCallType) : bool
{
    /**
     * Indica si un usuario tiene Videollamadas disponibles acorde al tipo de la misma
     */


    $typePlanSlug = '';

    if(Auth::user()->hasRole('usuario.free'))
        $typePlanSlug = 'usuario.free';

    if(Auth::user()->hasRole('cliente.registrado'))
        $typePlanSlug = 'cliente.registrado';

    if(Auth::user()->hasRole('usuario.lite'))
        $typePlanSlug = 'usuario.lite';

    if(Auth::user()->hasRole('usuario.estandar.mayor'))
        $typePlanSlug = 'usuario.estandar.mayor';

    if(Auth::user()->hasRole('usuario.estandar.menor'))
        $typePlanSlug = 'usuario.estandar.menor';

    if(Auth::user()->hasRole('usuario.premium.mayor'))
        $typePlanSlug = 'usuario.premium.mayor';

    if(Auth::user()->hasRole('usuario.premium.menor'))
        $typePlanSlug = 'usuario.premium.menor';

    if(Auth::user()->hasRole('cliente.fase.evaluacion'))
        $typePlanSlug = 'cliente.fase.evaluacion';

    if(Auth::user()->hasRole('cliente.fase.analisis'))
        $typePlanSlug = 'cliente.fase.analisis';

    if(Auth::user()->hasRole('cliente.fase.ejecucion'))
        $typePlanSlug = 'cliente.fase.ejecucion';

    if(Auth::user()->hasRole('cliente.fase.asesoramiento.integral'))
        $typePlanSlug = 'cliente.fase.asesoramiento.integral';

    $dateNow = Carbon::now();
    $oneTimeAgo;

    if(Auth::user()->hasRole(['cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral']))
        $oneTimeAgo = $dateNow->subMonth();
    else
        $oneTimeAgo = $dateNow->subWeek();

    $numOneTimeAgo = VideoCall::where('client_id', Auth::user()->client->id)
                                    ->whereIn('status', ['Por atender', 'Atendida'])
                                    ->where('created_at', '>=', $oneTimeAgo);
    /**
     * No tomo en cuenta las llamadas Canceladas ni las No atendidas. Porque estos Status deberían ocurrir por parte de C&A y no del cliente
     * Las videollamadas NO se renuevan solo se calcula en base a la última semana
     */
    if($videoCallType == 1)
    {
        $videoCallsAvailable = (Auth::user()->hasRole(['cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral']))
                                ? monthlyVideoCallsType1AvailableByRoles($typePlanSlug)
                                : weeklyVideoCallsType1AvailableByRoles($typePlanSlug);


        if($videoCallsAvailable == 0) return false;

        $numOneTimeAgo = $numOneTimeAgo->where('video_call_type_id', 1)
                                        ->count();

        if($numOneTimeAgo >= $videoCallsAvailable)
            return false;
    }

    if($videoCallType == 2)
    {
        $videoCallsAvailable = (Auth::user()->hasRole(['cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral']))
                                ? monthlyVideoCallsType2AvailableByRoles($typePlanSlug)
                                : weeklyVideoCallsType2AvailableByRoles($typePlanSlug);

        if($videoCallsAvailable == 0) return false;

        $numOneTimeAgo = $numOneTimeAgo->where('video_call_type_id', 2)
                                        ->count();

        if($numOneTimeAgo >= $videoCallsAvailable)
            return false;
    }

    return true;
}

/**
 * Estas dos funciones se realizan para evitar crear una Tabla en la BD más
 */

function weeklyVideoCallsType1AvailableByRoles($slugRole) : int
{
    switch($slugRole)
    {
        case 'usuario.free':  return 0; break;
        case 'cliente.registrado': return 0; break;
        case 'usuario.lite': return 1; break;
        case 'usuario.estandar.menor': return 1; break;
        case 'usuario.estandar.mayor': return 2; break;
        case 'usuario.premium.menor': return 3; break;
        case 'usuario.premium.mayor': return 5; break;
        default:    return 0;
    }
}

function weeklyVideoCallsType2AvailableByRoles($slugRole) : int
{

    switch($slugRole)
    {
        case 'usuario.free': return 0; break;
        case 'cliente.registrado': return 0; break;
        case 'usuario.lite': return 0; break;
        case 'usuario.estandar.menor': return 1; break;
        case 'usuario.estandar.mayor': return 2; break;
        case 'usuario.premium.menor': return 3; break;
        case 'usuario.premium.mayor': return 5; break;
        default:    return 0;
    }

}


function monthlyVideoCallsType1AvailableByRoles($slugRole) : int
{
    switch($slugRole)
    {
        case 'cliente.fase.evaluacion':
        case 'cliente.fase.analisis':
        case 'cliente.fase.ejecucion':
        case 'cliente.fase.asesoramiento.integral': return 2; break;
        default:    return 0;
    }
}

function monthlyVideoCallsType2AvailableByRoles($slugRole) : int
{
    //TODO: Por ahora retorna 0 debido al COVID-19
    switch($slugRole)
    {
        case 'cliente.fase.evaluacion':
        case 'cliente.fase.analisis':
        case 'cliente.fase.ejecucion':
        case 'cliente.fase.asesoramiento.integral': return 0; break;
        default:    return 0;
    }

}

