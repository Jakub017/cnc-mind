<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;

class FilesController extends Controller
{
    public function download(File $file)
    {
        if(auth()->id() == $file->user_id) {
            return Storage::download($file->path, $file->name);
        } else {
            abort(403);
        }
    }
}
