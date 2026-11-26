<?php

namespace App\Policies;

use App\Models\CertificadoSolvencia;
use App\Models\User;

class CertificadoSolvenciaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdministrador() || $user->esRecaudador();
    }

    public function view(User $user, CertificadoSolvencia $certificado): bool
    {
        return $user->esAdministrador() || $user->esRecaudador();
    }

    public function create(User $user): bool
    {
        return $user->esAdministrador() || $user->esRecaudador();
    }

    public function update(User $user, CertificadoSolvencia $certificado): bool
    {
        return false;
    }

    public function delete(User $user, CertificadoSolvencia $certificado): bool
    {
        return false;
    }

    public function restore(User $user, CertificadoSolvencia $certificado): bool
    {
        return false;
    }

    public function forceDelete(User $user, CertificadoSolvencia $certificado): bool
    {
        return false;
    }

    public function anular(User $user, CertificadoSolvencia $certificado): bool
    {
        return $user->esAdministrador();
    }
}
