<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Models\VisualFormComponent;

abstract class Component
{
    public function __construct(private readonly ?VisualFormComponent $record = null) {}

    abstract public function getOptionName(): string;

    abstract public function getSupportedProps(): array;

    public function getProps(): array
    {
        $component = $this->record;
        $supported = $this->getSupportedProps();
        $props = [];
        if ($component) {
            $componentProps = collect($component->getAttribute('props'));
            foreach ($supported as $prop) {
                $props[$prop] = $componentProps->get($prop);
            }
        }

        return $props;
    }

    public function getRecord(): ?VisualFormComponent
    {
        return $this->record;
    }

    abstract public function makeComponent();

    abstract public function getBackendSchema(): array;
}
