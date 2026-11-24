<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use SensitiveParameter;

class ActiveUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(#[SensitiveParameter] array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        return $user?->getAttribute('estado') === 'ACTIVO' ? $user : null;
    }
}
