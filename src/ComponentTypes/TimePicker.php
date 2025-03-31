<?php

namespace Coolsam\VisualForms\ComponentTypes;

class TimePicker extends Field
{
    public function getOptionName(): string
    {
        return __('Time Picker');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\TimePicker
    {
        return \Filament\Forms\Components\TimePicker::make($name);
    }
}
