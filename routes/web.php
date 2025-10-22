<?php

use App\Http\Controllers\LinkedinController;
use App\Livewire\LinkedinDashboard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

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

    // LinkedIn Automation Routes
    Route::get('/linkedin', LinkedinDashboard::class)->name('linkedin.dashboard');
    Route::get('/auth/linkedin', [LinkedinController::class, 'redirect'])->name('linkedin.auth');
    Route::get('/auth/linkedin/callback', [LinkedinController::class, 'callback'])->name('linkedin.callback');
    
    // Analytics Routes
    Route::get('/analytics', \App\Livewire\AnalyticsDashboard::class)->name('analytics.dashboard');
});

require __DIR__.'/auth.php';
