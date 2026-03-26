<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\File as FileStorage;

class ContractStepFilesController extends Controller {
    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'required',
            'contract_steps_id' => 'required',
        ]);
        $uploadedFile = $request->file('file');
        $path = public_path('files/contracts');

        if (!is_dir($path)) FileStorage::makeDirectory($path);

        $file = new File();
        $file->original_name = $uploadedFile->getClientOriginalName();
        $file->extension = $uploadedFile->extension();
        $file->size = $uploadedFile->getSize();
        $file->mime_type = $uploadedFile->getMimeType();
        $file->path = 'files/contracts';
        $file->full_path = 'files/contracts/' . $uploadedFile->getClientOriginalName() . '.' . $uploadedFile->extension();
        $file->contract_steps_id = $request->input('contract_steps_id');
        $file->save();
        $uploadedFile->move($path, $uploadedFile->getClientOriginalName() .'.'. $uploadedFile->extension());
    }
    public function destroy($id) {
        $file = File::find($id);
        $path = public_path($file->full_path);
        if (FileStorage::exists($path)) FileStorage::delete($path);
        $file->delete();
        return response()->json(['status' =>  'success'], 200);
    }
}