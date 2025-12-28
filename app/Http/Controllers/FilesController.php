<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;
use App\Models\Operation;

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

    public function downloadOperationPdf(Operation $operation)
    {
        return Pdf::view('pdfs.operation', ['operation' => $operation])
            ->format('a4')
            ->name("{$operation->name}.pdf");
    }
}
