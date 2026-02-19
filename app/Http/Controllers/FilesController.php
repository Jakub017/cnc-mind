<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Operation;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class FilesController extends Controller
{
    public function download(File $file)
    {
        if (auth()->id() == $file->user_id) {
            return Storage::download($file->path, $file->name);
        } else {
            abort(403);
        }
    }

    public function downloadOperationPdf(Operation $operation)
    {
        return Pdf::view('pdfs.operation', ['operation' => $operation])
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->setNodeBinary(config('services.browsershot.node_path'))
                    ->setNpmBinary(config('services.browsershot.npm_path'))
                    ->noSandbox()
                    ->addCustomFlags(['--disable-setuid-sandbox']);
            })
            ->name("{$operation->name}.pdf");
    }
}
