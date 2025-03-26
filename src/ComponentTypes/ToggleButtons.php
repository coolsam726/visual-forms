<?php

namespace Coolsam\VisualForms\ComponentTypes;

class ToggleButtons extends Radio
{
    public function getOptionName(): string
    {
        return __('Toggle Buttons');
    }

    public function makeComponent(bool $editable = false)
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\ToggleButtons::make($record->getAttribute('name'));
        $this->configureComponent($component);

        $this->makeEditableAction($component, $editable);

        return $component;
    }
}
