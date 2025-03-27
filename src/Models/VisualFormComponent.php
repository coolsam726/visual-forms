<?php

namespace Coolsam\VisualForms\Models;

use Config;
use Coolsam\VisualForms\Utils;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class VisualFormComponent extends Model implements Sortable
{
    use HasUlids;
    use NodeTrait;
    use SortableTrait;

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

    public function determineOrderColumnName(): string
    {
        return 'sort_order';
    }

    public function buildSortQuery()
    {
        return static::query()->where('is_active', '=', true)
            ->where('parent_id', $this->getAttribute('parent_id'));
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

    public function createChild(array $data)
    {
        $data['form_id'] = $this->getAttribute('form_id');

        return $this->children()->create($data);
    }

    protected static function booted() {}
}
