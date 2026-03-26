<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderBusinessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el campo order de todos los negocios';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $businesses = Business::where('businesses.id', '>=', 596)
                                ->get();

        foreach ($businesses as $business)
        {
            $business->random_string = Str::random(12);
            $business->save();
        }
    }
}
