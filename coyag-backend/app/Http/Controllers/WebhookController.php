<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Jobs\BusinessCheckerHook;
use App\Jobs\BusinessImporterHook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class WebhookController extends Controller 
{
    public function CheckHook    (Request $request) {
        $filename = Carbon::now()->format('d-m-Y') . ".xlsx";
        if ($request->file('file')) {
            Storage::put('check-webhook/' . $filename, File::get($request->file('file')));
        } else {
            Storage::put('check-webhook/' . $filename, $request->getContent());
        }
        $file = storage_path('app/check-webhook/' . $filename);

        BusinessCheckerHook::dispatchAfterResponse($file, $request->input('property_type', 1));

        return response()->json(['status' => 'success'], 200);
    }

    public function ImporterHook (Request $request) {
        $filename = Carbon::now()->format('d-m-Y') . ".xlsx";
        if ($request->file('file')) {
            Storage::put('import-webhook/' . $filename, File::get($request->file('file')));
        } else {
            Storage::put('import-webhook/' . $filename, $request->getContent());
        }
        $file = storage_path('app/import-webhook/' . $filename);

        BusinessImporterHook::dispatchAfterResponse($file, $request->input('update_image', 0), $request->input('update_field', 1));

        return response()->json(['status' => 'success'], 200);
    }
}

