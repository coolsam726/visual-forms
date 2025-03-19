<?php

namespace Coolsam\VisualForms\ComponentTypes;

class ToggleButtons extends Radio
{
    public function getOptionName(): string
    {
        return __('Toggle Buttons');
    }

    public function makeComponent()
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\ToggleButtons::make($record->getAttribute('name'));
        $this->configureComponent($component);

        return $component;
    }
}
