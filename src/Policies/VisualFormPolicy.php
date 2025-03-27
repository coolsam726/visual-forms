<?php

namespace Coolsam\VisualForms\Policies;

use Coolsam\VisualForms\Models\VisualForm;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class VisualFormPolicy
{
    use HandlesAuthorization;

    public function viewAny(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.viewAny', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function view(Authenticatable $user, VisualForm $visualForm): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.view', function (Authenticatable $user, VisualForm $visualForm) {
            return false;
        })($user, $visualForm);
    }

    public function create(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.create', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function update(Authenticatable $user, VisualForm $visualForm): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.update', function (Authenticatable $user, VisualForm $visualForm) {
            return false;
        })($user, $visualForm);
    }

    public function delete(Authenticatable $user, VisualForm $visualForm): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.delete', function (Authenticatable $user, VisualForm $visualForm) {
            return false;
        })($user, $visualForm);
    }

    public function deleteAny(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.deleteAny', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function restore(Authenticatable $user, VisualForm $visualForm): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.restore', function (Authenticatable $user, VisualForm $visualForm) {
            return false;
        })($user, $visualForm);
    }

    public function forceDelete(Authenticatable $user, VisualForm $visualForm): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.forceDelete', function (Authenticatable $user, VisualForm $visualForm) {
            return false;
        })($user, $visualForm);
    }

    public function reorder(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-forms.reorder', function (Authenticatable $user) {
            return false;
        })($user);
    }
}
