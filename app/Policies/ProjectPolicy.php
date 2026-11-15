<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isResponsableStudio() || $user->isSecretariat() || $user->isArtiste();
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isAdmin() || $user->isResponsableStudio() || $user->isSecretariat()) {
            return true;
        }

        if ($user->isArtiste()) {
            return $project->artist_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isResponsableStudio();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() || ($user->isResponsableStudio() && $project->studio_manager_id === $user->id);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}
