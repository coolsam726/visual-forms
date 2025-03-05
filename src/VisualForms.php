<?php

namespace Coolsam\VisualForms;

use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormField;
use Filament\Forms\Components\Field;
use Illuminate\Support\Collection;

class VisualForms
{
    // Enum of control types
    public function getControlTypeOptions(): Collection
    {
        // get options from ControlTypes enum, as a key value array
        return collect(ControlTypes::cases())->pluck('name')->mapWithKeys(function ($value, $key) {
            return [$value => $value];
        });
    }

    /**
     * @return Field[]
     */
    public function makeSchema(VisualForm $form): array
    {
        $schema = [];
        foreach ($form->fields as $field) {
            /**
             * @var VisualFormField $field
             */
            // TODO: Process the logic
        }

        return $schema;
    }
}
