<?php

namespace Coolsam\VisualForms\Policies;

use Coolsam\VisualForms\Models\VisualFormComponent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class VisualFormComponentPolicy
{
    use HandlesAuthorization;

    public function viewAny(Authenticatable $user)
    {
        return \Config::get('visual-forms.policies.visual-form-components.viewAny')($user);
    }

    public function view(Authenticatable $user, VisualFormComponent $model)
    {
        return \Config::get('visual-forms.policies.visual-form-components.view')($user, $model);
    }

    public function create(Authenticatable $user)
    {
        return \Config::get('visual-forms.policies.visual-form-components.create')($user);
    }

    public function update(Authenticatable $user, mixed $model)
    {
        return \Config::get('visual-forms.policies.visual-form-components.update')($user, $model);
    }

    public function delete(Authenticatable $user, mixed $model)
    {
        return \Config::get('visual-forms.policies.visual-form-components.delete')($user, $model);
    }

    public function deleteAny(Authenticatable $user, mixed $model)
    {
        return \Config::get('visual-forms.policies.visual-form-components.deleteAny')($user, $model);
    }

    public function reorder(Authenticatable $user, mixed $model)
    {
        return \Config::get('visual-forms.policies.visual-form-components.reorder')($user, $model);
    }
}
