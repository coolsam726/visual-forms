<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Concerns\HasOptions;

class Select extends Field
{
    use HasOptions;

    public function getOptionName(): string
    {
        return __('Select');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\Select
    {
        return \Filament\Forms\Components\Select::make($name);
    }
}
