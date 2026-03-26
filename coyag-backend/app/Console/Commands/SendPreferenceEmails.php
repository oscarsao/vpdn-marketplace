<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\Client;
use App\Traits\PreferenceTrait;
use App\Mail\BusinessPreferenceMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendPreferenceEmails extends Command
{
    use PreferenceTrait;

    /*
        Recordando que las Preferencias de Negocios de los Clientes se encuentran
        en la relación business_clients, no confundir con recomendaciones
    */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preference:sendEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos electrónicos a los clientes sobre negocios agregados o editados, en la última semana, que cumplan con sus preferencias';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $emails = [];

        $businesses = Business::where(function ($query) {
                                    $query->where('recommendation_finished_at', '<', Carbon::now()->format('Y-m-d'))
                                        ->orWhereNull('recommendation_finished_at');
                                })
                                ->where('updated_at', '>', Carbon::now()->subDays(7))
                                ->where('flag_active', true)
                                ->get();

        foreach($businesses as $business)
        {
            $arrClients = PreferenceTrait::checkBusinessClientPreference($business);

            foreach($arrClients as $clientId)
            {
                if(isset($emails[$clientId]))
                    array_push($emails[$clientId], $business->id);
                else
                    $emails[$clientId] = [$business->id];
            }

        }

        foreach($emails as $key => $value)
        {
            $client = Client::find($key);
            $businessesPerClient = [];

            foreach($value as $val)
            {

                $business = Business::where('businesses.id', $val)
                                        ->select('id', 'name', 'description')
                                        ->addSelect(['image_1' => BusinessMultimedia::select('small_image_path')
                                            ->whereColumn('business_id', 'businesses.id')
                                            ->where('type', 'image')
                                            ->orderBy('id', 'asc')
                                            ->skip(0)
                                            ->take(1)
                                        ])
                                        ->first();

                // TODO: Aquí debería calcularse con env('APP_URL')
                $business->image_1 = "https://api.cohenyaguirre.es/" . $business->image_1;

                array_push($businessesPerClient, $business);
            }


            try {
                if($client->is_subscribed_biz_pref)
                {
                    $token = Str::random(24);
                    $client->biz_pref_unsubscribe_token = $token;
                    $client->save();
                    Mail::to($client->user->email)->send(new BusinessPreferenceMail($client, $businessesPerClient, $token));
                }
            } catch(Exception $e) {
                Log::error("Error - COYAG -> $e");
            }

        }


    }
}
