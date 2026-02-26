<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

class Login extends BaseLogin
{
    /**
     * @return string | View
     */
    #[Layout('components.layouts.auth')]
    public function render(): View
    {
        return view('filament.pages.auth.login');
    }
}
