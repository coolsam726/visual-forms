<?php

namespace Coolsam\VisualForms\ComponentTypes;

class DateTimePicker extends Field
{
    public function getOptionName(): string
    {
        return __('Date-Time Picker');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\DateTimePicker
    {
        return \Filament\Forms\Components\DateTimePicker::make($name);
    }
}
{}
