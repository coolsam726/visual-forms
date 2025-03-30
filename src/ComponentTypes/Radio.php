<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Concerns\HasOptions;

class Radio extends Field
{
    use HasOptions;
    public function getOptionName(): string
    {
        return __('Radio');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\Radio
    {
        return \Filament\Forms\Components\Radio::make($name);
    }
}
