<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Filament\Components\ExtendedFieldset;

class Fieldset extends Layout
{
    public function getOptionName(): string
    {
        return __('Fieldset');
    }

    public function letThereBe(string $name): ExtendedFieldset
    {
        return ExtendedFieldset::make($name);
    }
}
