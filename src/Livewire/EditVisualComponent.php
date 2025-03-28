<?php

namespace Coolsam\VisualForms\Livewire;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @property Form $form
 */
class EditVisualComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public VisualFormComponent $record;

    public function mount(int $id): void
    {
        $this->record = VisualFormComponent::findOrFail($id);
        $this->form->fill($this->record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(VisualFormComponentResource::getSchema())
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
    }

    public function render(): string
    {
        return view('visual-forms::livewire.edit-visual-component');
    }
}
