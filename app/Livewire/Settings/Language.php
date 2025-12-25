<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class Language extends Component
{
    public string $locale;

    public function mount()
    {
        $this->locale = session()->get('locale', 'en');
        App::setLocale($this->locale);
    }

    public function updatedLocale($locale)
    {
        session()->put('locale', $locale);
        App::setLocale($locale);
        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.settings.language');
    }
}
