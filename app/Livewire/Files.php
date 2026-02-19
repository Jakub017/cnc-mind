<?php

namespace App\Livewire;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Files extends Component
{
    use WithFileUploads, WithPagination;

    #[Validate('required|file|mimes:pdf,doc,docx|max:51200')]
    public $file;

    public function updatedFile()
    {
        $this->validate();

        File::create([
            'user_id' => auth()->id(),
            'path' => $this->file->store(path: auth()->id().'/files'),
            'name' => $this->file->getClientOriginalName(),
            'type' => $this->file->getClientOriginalExtension(),
            'size' => $this->file->getSize(),
        ]);

        $this->modal('add-file')->close();
        Toaster::success(__('File has been successfully added.'));
    }

    public function deleteFile(File $file)
    {
        auth()->user()->files()->where('id', $file->id)->delete();
        Storage::delete($file->path);
        Toaster::success(__('File has been successfully deleted.'));
    }

    public function render()
    {
        $files = auth()->user()->files()->paginate(5);

        return view('livewire.files', compact('files'));
    }
}
