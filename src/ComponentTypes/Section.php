<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms;

class Section extends Layout
{
    public function getOptionName(): string
    {
        return __('Section');
    }

    public function letThereBe(string $name): Forms\Components\Section
    {
        return Forms\Components\Section::make($name);
    }
}
