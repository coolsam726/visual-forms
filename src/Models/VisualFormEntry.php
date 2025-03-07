<?php

namespace Coolsam\VisualForms\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisualFormEntry extends Model
{
    use HasUlids;
    use SoftDeletes;

    protected $guarded = ['id', 'ulid'];

    protected $casts = [
        'payload' => 'array',
        'is_processed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(config('visual-forms.models.visual_form'), 'form_id');
    }
}
