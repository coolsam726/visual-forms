<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms;

class ColorPicker extends Field
{
    public function getOptionName(): string
    {
        return __('Color Picker');
    }

    public function letThereBe(string $name): Forms\Components\Component|Forms\Components\ColorPicker
    {
        return Forms\Components\ColorPicker::make($name);
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            ...parent::getSpecificBasicSchema(),
            Forms\Components\ToggleButtons::make('defaultColorFormat')
                ->label(__('Default Color Format'))
                ->options([
                    'hex' => __('Hex'),
                    'rgb' => __('RGB'),
                    'hsl' => __('HSL'),
                    'rgba' => __('RGBA'),
                ])->columnSpanFull()->inline(),
        ];
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);

        $props = $this->getProps();

        if (filled($props->get('defaultColorFormat'))) {
            switch ($props->get('defaultColorFormat')) {
                case 'rgb':
                    if (method_exists($component, 'rgb')) {
                        $component
                            ->rgb()
                            ->regex('/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/');
                    }
                    break;
                case 'hsl':
                    if (method_exists($component, 'hsl')) {
                        $component
                            ->hsl()
                            ->regex('/^hsl\(\s*(\d+)\s*,\s*(\d*(?:\.\d+)?%)\s*,\s*(\d*(?:\.\d+)?%)\)$/');
                    }
                    break;
                case 'rgba':
                    if (method_exists($component, 'rgba')) {
                        $component
                            ->rgba()
                            ->regex('/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/');
                    }
                    break;
                default:
                    if (method_exists($component, 'hex')) {
                        $component
                            ->hex()
                            ->regex('/^#([a-f0-9]{6}|[a-f0-9]{3})\b$/');
                    }
                    break;
            }
        }
    }
}
