<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Select extends Component
{
    public function getOptionName(): string
    {
        return __('Select');
    }

    public function isLayout(): bool
    {
        return true;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    public function makeComponent(): array
    {
        return [];
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([]);
    }

    public function getValidationSchema(): array
    {
        return [];
    }
}
