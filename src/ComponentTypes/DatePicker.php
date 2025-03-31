<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms;

class DatePicker extends Field
{
    public function getOptionName(): string
    {
        return __('Date Picker');
    }

    public function letThereBe(string $name): Forms\Components\Component
    {
        return Forms\Components\DatePicker::make($name);
    }
}
