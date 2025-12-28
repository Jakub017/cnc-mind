<?php


use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Language;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Operations;
use App\Livewire\Materials;
use App\Livewire\Tools;
use App\Livewire\Files;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    // My routes
    Route::get('/tools', Tools::class)->name('tools');
    Route::get('/materials', Materials::class)->name('materials');
    Route::get('/files', Files::class)->name('files');
    Route::get('/operations', Operations::class)->name('operations');

    Route::controller(FilesController::class)->group(function() {
        Route::get('/files/download/{file}', 'download')->name('file.download');
        Route::get('/operations/download/{operation}', 'downloadOperationPdf')->name('operation.download');
    });
    

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
    Route::get('settings/language', Language::class)->name('language.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
