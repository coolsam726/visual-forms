<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Textarea extends Field
{
    public function getOptionName(): string
    {
        return __('Textarea');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\Textarea
    {
        return new \Filament\Forms\Components\Textarea($name);
    }
}
