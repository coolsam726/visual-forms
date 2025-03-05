<?php

namespace Coolsam\VisualForms\Models;

use Illuminate\Database\Eloquent\Model;

class VisualFormEntry extends Model
{
    protected $guarded = ['id'];

    public function form()
    {
        return $this->belongsTo(config('visual-forms.models.visual_form'));
    }
}
