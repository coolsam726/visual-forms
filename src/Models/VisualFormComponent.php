<?php

namespace Coolsam\VisualForms\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class VisualFormComponent extends Model
{
    use HasUlids;
    use NodeTrait;

    protected $guarded = ['id', 'ulid'];

    protected $casts = [
        'props' => 'array',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
        'columns' => 'array',
        'column_span' => 'array',
        'column_start' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function visualForm(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Config::get('visual-forms.models.visual_form'), 'form_id');
    }

    public function getProps()
    {
        $class = $this->getAttribute('component_type');
        return (new $class)->getProps($this);
    }
}
