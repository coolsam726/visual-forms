<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Concerns\HasOptions;
use Filament\Forms;

class ToggleButtons extends Field
{
    use HasOptions;
    public function getOptionName(): string
    {
        return __('Toggle Buttons');
    }

    public function letThereBe(string $name): Forms\Components\ToggleButtons
    {
        return Forms\Components\ToggleButtons::make($name);
    }
}
