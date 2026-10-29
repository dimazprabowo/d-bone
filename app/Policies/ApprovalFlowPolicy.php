<?php

namespace App\Policies;

use App\Models\ApprovalFlow;
use App\Models\User;

class ApprovalFlowPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('approval_flow_view');
    }

    public function view(User $user, ApprovalFlow $flow): bool
    {
        return $user->can('approval_flow_view');
    }

    public function create(User $user): bool
    {
        return $user->can('approval_flow_create');
    }

    public function update(User $user, ApprovalFlow $flow): bool
    {
        return $user->can('approval_flow_update');
    }

    public function delete(User $user, ApprovalFlow $flow): bool
    {
        return $user->can('approval_flow_delete');
    }
}
