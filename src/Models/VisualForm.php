<?php

namespace Coolsam\VisualForms\Models;

use Coolsam\VisualForms\Facades\VisualForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Pure;

class VisualForm extends Model
{
    protected $guarded = ['id'];

    public function fields(): HasMany
    {
        return $this->hasMany(\Config::get('visual-forms.models.visual_form_field'), 'form_id');
    }

    #[Pure]
    public function schema()
    {
        return VisualForms::makeSchema($this);
    }
}
