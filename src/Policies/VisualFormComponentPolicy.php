<?php

namespace Coolsam\VisualForms\Policies;

use Coolsam\VisualForms\Models\VisualFormComponent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class VisualFormComponentPolicy
{
    use HandlesAuthorization;

    public function viewAny(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.viewAny', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function view(Authenticatable $user, VisualFormComponent $visualFormComponent): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.view', function (Authenticatable $user, VisualFormComponent $visualFormComponent) {
            return false;
        })($user, $visualFormComponent);
    }

    public function create(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.create', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function update(Authenticatable $user, VisualFormComponent $visualFormComponent): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.update', function (Authenticatable $user, VisualFormComponent $visualFormComponent) {
            return false;
        })($user, $visualFormComponent);
    }

    public function delete(Authenticatable $user, VisualFormComponent $visualFormComponent): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.delete', function (Authenticatable $user, VisualFormComponent $visualFormComponent) {
            return false;
        })($user, $visualFormComponent);
    }

    public function deleteAny(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.deleteAny', function (Authenticatable $user) {
            return false;
        })($user);
    }

    public function restore(Authenticatable $user, VisualFormComponent $visualFormComponent): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.restore', function (Authenticatable $user, VisualFormComponent $visualFormComponent) {
            return false;
        })($user, $visualFormComponent);
    }

    public function forceDelete(Authenticatable $user, VisualFormComponent $visualFormComponent): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.forceDelete', function (Authenticatable $user, VisualFormComponent $visualFormComponent) {
            return false;
        })($user, $visualFormComponent);
    }

    public function reorder(Authenticatable $user): bool
    {
        return \Config::get('visual-forms.policies.visual-form-components.reorder', function (Authenticatable $user) {
            return false;
        })($user);
    }
}
