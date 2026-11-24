<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use SensitiveParameter;

class ResetPassword extends BaseResetPassword
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'correo' => $data['email'],
            'password' => $data['password'],
            'token' => $data['token'],
        ];
    }
}
