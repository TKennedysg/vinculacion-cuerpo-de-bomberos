<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('nombres')
            ->label('Nombres completos')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('correo')
            ->label('Correo electrónico')
            ->email()
            ->required()
            ->maxLength(255)
            ->live(debounce: 500);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label('Contraseña actual')
            ->password()
            ->autocomplete('current-password')
            ->currentPassword(guard: Filament::getAuthGuard())
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')) || ($get('correo') !== $this->getUser()->getAttributeValue('correo')))
            ->dehydrated(false);
    }

    protected function handleRecordUpdate(Model $record, #[SensitiveParameter] array $data): Model
    {
        unset($data['passwordConfirmation'], $data['currentPassword']);

        if (array_key_exists('password', $data)) {
            $data['password_hash'] = $data['password'];
            unset($data['password']);
        }

        $record->update($data);

        return $record;
    }
}
