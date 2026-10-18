<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPhoto;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function destroy(ProductPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('status', 'Foto dihapus.');
    }
}
