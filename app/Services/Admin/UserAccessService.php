<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\Password;

class UserAccessService
{
    public function __construct(private AuditLogService $audit) {}

    public function sendReset(User $target, User $actor): string
    {
        $result = Password::sendResetLink(['email' => $target->email]);
        $this->audit->record('user.password_reset_requested', $actor, $target, $target);

        return $result;
    }
}
