<?php

namespace Coolsam\VisualForms\Filament\Components;

use Filament\Forms\Components\Contracts\HasHeaderActions;
use Filament\Forms\Components\Fieldset;

class ExtendedFieldset extends Fieldset implements HasHeaderActions
{
    use \Filament\Forms\Components\Concerns\HasHeaderActions;

    public function getView(): string
    {
        return 'visual-forms::livewire.extended-fieldset';
    }

    public function getId(): ?string
    {
        $id = parent::getId();

        if (filled($id)) {
            return $id;
        }

        $heading = $this->getLabel();

        if (blank($heading)) {
            return null;
        }

        $id = \Str::slug($heading);

        if ($statePath = $this->getStatePath()) {
            $id = "{$statePath}.{$id}";
        }

        return $id;
    }

    public function getKey(): ?string
    {
        return parent::getKey() ?? ($this->getActions() ? $this->getId() : null);
    }
}
