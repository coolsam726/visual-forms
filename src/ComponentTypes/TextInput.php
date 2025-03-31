<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms\Get;

class TextInput extends Field
{
    public function getOptionName(): string
    {
        return __('Text Input');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\TextInput
    {
        return \Filament\Forms\Components\TextInput::make($name);
    }
}
