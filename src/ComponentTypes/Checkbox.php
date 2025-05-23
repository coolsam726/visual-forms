<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms;

class Checkbox extends Field
{
    public function getOptionName(): string
    {
        return __('Checkbox');
    }

    public function letThereBe(string $name): Forms\Components\Checkbox | Forms\Components\Component
    {
        return Forms\Components\Checkbox::make($name);
    }
}
