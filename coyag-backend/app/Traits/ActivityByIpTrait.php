<?php

namespace App\Traits;

use App\Models\ActivityByIp;
use Carbon\Carbon;

trait ActivityByIpTrait
{

    public function storeActivityByIP($ip, $activity, $status, $userId = null)
    {

        $activityIp = new ActivityByIp();

        $activityIp->ip = $ip;
        $activityIp->activity = $activity;
        $activityIp->status = $status;
        if(!empty($userId))
            $activityIp->user_id = $userId;

        $activityIp->save();
    }

    public function createClientLimitIP($ip) : bool
    {
        /**
         * Retorna true cuando la IP ya ha realizado dos
         * registros de clientes en un día
         */

        if($ip == '127.0.0.1')
            return false;

        $auxCount = ActivityByIp::where('ip', $ip)
                                ->where('activity', 'create_client')
                                ->where('status', 'complete')
                                ->where('created_at', '>', Carbon::now()->subDays(1))
                                ->count();

        return  $auxCount >= 2 ? true : false;

    }

}

