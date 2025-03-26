<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Widgets;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

class FieldEditor extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
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
            ->schema(fn (VisualForm $record) => [
                Actions::make([
                    Actions\Action::make('create_child')->label(__('Add Top-Level Component'))
                        ->color('success')->icon('heroicon-o-plus-circle')
                        ->form(fn (Form $form) => $form
                            ->model(VisualFormComponent::class)
                            ->schema(VisualFormComponentResource::getSchema()))
                        ->mountUsing(fn (
                            ComponentContainer $form
                        ) => $form->fill(['form_id' => $this->record?->getKey()]))->action(function (array $data) {
                            // Create visual form
                            return $this->record->children()->create($data);
                        }),
                ]),
                ...$record->schema(editable: true),
            ]);
    }
}
