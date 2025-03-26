<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Widgets;

use Coolsam\VisualForms\Models\VisualForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

class FieldEditor extends Widget implements HasForms
{
    use InteractsWithForms;

    public ?VisualForm $record = null;

    public ?array $data = [];

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'visual-forms::filament.resources.visual-form-resource.widgets.field-editor';

    public function form(Form $form): Form
    {
        return $form
            ->model($this->record)
            ->statePath('data')
            ->schema(fn (VisualForm $record) => $record->schema(editable: true));
    }
}
