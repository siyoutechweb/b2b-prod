<?php namespace App\Http\Controllers;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ProfilesController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function uploadCoverImg(Request $request)
    {
        $supplier = AuthController::me();
            if ($request->hasFile('cover_img')) {
                if ($supplier->cover_img_url !== null and $supplier->cover_img_url !== null) {
                    $isImageDeleted = Storage::disk('google')->delete('profils/' .  $supplier->cover_img_name);
                }
                $path = $request->file('cover_img')->store('profils', 'google');
                $fileUrl = Storage::url($path);
                $supplier->cover_img_url = $fileUrl;
                $supplier->cover_img_name = basename($path);
                $supplier->save();
                return response()->json(['URL' => $supplier->cover_img_url], 200);
            }
        return response()->json(['msg' => 'Error'], 500);
    }

}
