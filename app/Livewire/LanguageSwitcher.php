<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $locale;

    public function mount()
    {
        $this->locale = session()->get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }

    #[Computed]
    public function languageLabel()
    {
        return match($this->locale) {
            'pl' => ['name' => __('Polish'), 'flag' => 'pl'],
            'en' => ['name' => __('English'), 'flag' => 'gb'],
            'de' => ['name' => __('German'), 'flag' => 'de'],
            'it' => ['name' => __('Italian'), 'flag' => 'it'],
            'fr' => ['name' => __('French'), 'flag' => 'fr'],
            default => ['name' => $this->locale, 'flag' => $this->locale],
        };
    }

    public function setLocale($locale)
    {
        session()->put('locale', $locale);
        App::setLocale($locale);
        return redirect()->to(url()->previous());
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
