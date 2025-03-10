<?php

namespace Coolsam\VisualForms\Models;

use Coolsam\VisualForms\Facades\VisualForms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisualForm extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(\Config::get('visual-forms.models.visual_form_field'), 'form_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(\Config::get('visual-forms.models.visual_form_entry'), 'form_id');
    }

    public function schema()
    {
        return VisualForms::schema($this);
    }

    public function recordSubmission(array $data, bool $isProcessed = false)
    {
        return VisualForms::recordSubmission($this, $data, $isProcessed);
    }
}
