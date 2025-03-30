<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms;

class Checkbox extends Field
{
    public function getOptionName(): string
    {
        return __('Checkbox');
    }

    public function letThereBe(string $name): Forms\Components\Checkbox
    {
        return Forms\Components\Checkbox::make($name);
    }
}
