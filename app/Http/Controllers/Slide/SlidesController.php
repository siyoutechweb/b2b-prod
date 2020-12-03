<?php namespace App\Http\Controllers\Slide;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Storage;
use App\Models\Slide;

class SlidesController extends Controller {


    public function uploadSlide(Request $request)
    {
        $supplier = AuthController::me();
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('slides', 'google');
            $fileUrl = Storage::url($path);
            $slide = new Slide () ;
            $slide->slide_url = $fileUrl;
            $slide->slide_name = basename($path);
            $slide->slide_title = $request->input('title');
            $slide->description = $request->input('description');
            $slide->supplier_id = $supplier->id;
            $slide->save();
            return response()->json(['msg' => 'Success'], 200);
        }
        return response()->json(['msg' => 'Error'], 500);
    }

    public function getSlides()
    {
        $supplier = AuthController::me();
        $slides= $supplier->slides;
        return response()->json(['slides' => $slides], 200); 
    }

    public function deleteSlide($id)
    {
        $slide = Slide::find($id);
        $isImageDeleted = Storage::disk('google')->delete('slides/' . $slide->slide_name);
        if ($isImageDeleted) {
            $slide->delete();
            return response()->json(['msg' => 'Image Deleted'], 200);
        }
        return response()->json(['msg' => 'Error'], 500);
    }

}
