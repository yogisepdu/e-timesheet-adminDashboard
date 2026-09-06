<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    /**
     * Custom login view.
     */
    protected string $view = 'filament.pages.auth.login';

    /**
     * Custom layout untuk menghilangkan
     * card/layout login bawaan Filament.
     */
    protected static string $layout = 'filament.pages.auth.layout';
}
