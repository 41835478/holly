<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminUserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($ability != 'delete' && $user->isSuperAdmin()) {
            return true;
        }
    }

    public function create(AdminUser $user)
    {
        return $user->isSuperAdmin();
    }

    public function view(AdminUser $user, AdminUser $adminUser)
    {
        return $user->id === $adminUser->id;
    }

    public function update(AdminUser $user, AdminUser $adminUser)
    {
        return $user->id === $adminUser->id;
    }

    public function delete(AdminUser $user, AdminUser $adminUser)
    {
        return $user->isSuperAdmin() && $user->id !== $adminUser->id;
    }
}
