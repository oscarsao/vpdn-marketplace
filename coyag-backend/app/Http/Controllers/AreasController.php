<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Area;
use Illuminate\Support\Facades\File;

class AreasController extends Controller {
    public function index() {
        $areas = Area::all();
        return response()->json($areas);
    }
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);
        $area = new Area;
        $area->name = $request->input('name');
        $area->description = $request->input('description');
        
        if ($request->has('image') && $request->file('image')) {
            $uploadedFile = $request->file('image');
            $path = public_path('areas');
            if (!is_dir($path)) {
                File::makeDirectory($path);
            }
            $fileName = $uploadedFile->getClientOriginalName();
            $uploadedFile->move($path, $fileName);
            $area->image = 'areas/' . $fileName;
        } else {
            $area->image = '';
        }
        

        $area->save();
        return response()->json($area);
    }
    public function update(Request $request, $id) {
        $area = Area::find($id);
        if (!$area) return response()->json(['message' => 'Area not found'], 404);
        if ($request->has('name')) {
            $area->name = $request->input('name');
        }
        if ($request->has('description')) {
            $area->description = $request->input('description');
        }
        if ($request->file('image')) {
            $uploadedFile = $request->file('image');
            $path = public_path('areas');
            if (!is_dir($path)) File::makeDirectory($path);
            $fileName = $uploadedFile->getClientOriginalName();
            $uploadedFile->move($path, $fileName);
            $area->image = 'areas/' . $fileName;
        }
        $area->save();
        return response()->json($area);
    }
    public function destroy($id) {
        $area = Area::find($id);
        if (!$area) return response()->json(['message' => 'Area not found'], 404);
        $area->delete();
        return response()->json(['message' => 'Area deleted successfully']);
    }
}