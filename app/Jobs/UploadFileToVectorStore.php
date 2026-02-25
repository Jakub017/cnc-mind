<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Stores;

class UploadFileToVectorStore implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $timeout = 300;

    public function backoff(): array
    {
        return [15, 30, 60, 120];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $fileId,
        public string $storeId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Stores::get($this->storeId);
        $file = File::find($this->fileId);
        $store->add(Document::fromPath(Storage::path($file->path)), metadata: [
            'file_id' => $file->id,
        ]);

        $file->update([
            'status' => 'indexed',
        ]);
    }
}
