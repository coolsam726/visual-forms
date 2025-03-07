<?php

namespace Coolsam\VisualForms\Models;

use Config;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisualFormField extends Model
{
    use HasUlids;

    protected $guarded = ['id', 'ulid'];

    protected $casts = [
        'validation_rules' => 'array',
        'extra_attributes' => 'array',
        'extra_props' => 'array',
        'required' => 'boolean',
        'options_from_db' => 'boolean',
        'options_where_conditions' => 'array',
        'options' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Config::get('visual-forms.models.visual_form'));
    }
}
