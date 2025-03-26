<?php

namespace Coolsam\VisualForms\Models;

use Config;
use Coolsam\VisualForms\Utils;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'state_conditions' => 'array',
        'column_span_full' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function visualForm(): BelongsTo
    {
        return $this->belongsTo(Config::get('visual-forms.models.visual_form'), 'form_id');
    }

    public function getProps()
    {
        $class = $this->getAttribute('component_type');

        return (new $class)->getProps($this);
    }

    public function makeComponent(bool $editable = false)
    {
        $class = $this->getAttribute('component_type');

        return Utils::instantiateClass($class, ['record' => $this])->makeComponent($editable);
    }
}
