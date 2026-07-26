<?php

namespace App\Actions;

use App\Models\User;

class DeleteUser
{
    public function delete(User $user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }
}
