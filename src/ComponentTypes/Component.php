<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Models\VisualFormComponent;

abstract class Component
{
    abstract public function getComponentType(): ControlTypes;
    abstract public function getSupportedProps(): array;
    public function getProps(?VisualFormComponent $component): array
    {
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

    abstract public function makeComponent(VisualFormComponent $component);
}