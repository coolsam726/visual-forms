<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Models\VisualFormComponent;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

class TextInput extends Component
{
    public function getSupportedProps(): array
    {
        return [
            'autocapitalize' => Boolean::class,
            'autocomplete' => Boolean::class,
            'length' => Integer::class,
            'maxLength' => Integer::class,
            'minLength' => Integer::class,
            'readOnly' => Boolean::class,
            'prefix' => String_::class,
            'suffix' => String_::class,
            'prefixIcon' => String_::class,
            'suffixIcon' => String_::class,
            'inlinePrefix' => Boolean::class,
            'inlineSuffix' => Boolean::class,
            'prefixIconColor' => String_::class,
            'suffixIconColor' => String_::class,
            'datalist' => Array_::class,
            'extraInputAttributes' => Array_::class,
            'inputMode' => String_::class, // See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#inputmode
            'placeholder' => String_::class,
            'step' => Integer::class,
            'currentPassword' => Boolean::class,
            'email' => Boolean::class,
            'integer' => Boolean::class,
            'mask' => String_::class,
            'maxValue' => Integer::class,
            'minValue' => Integer::class,
            'numeric' => Boolean::class,
            'password' => Boolean::class,
            'revealable' => Boolean::class,
            'tel' => Boolean::class,
            'telRegex' => String_::class,
            'type' => String_::class,
            'url' => Boolean::class,
        ];
    }

    public function getComponentType(): ControlTypes
    {
        return ControlTypes::TextInput;
    }

    public function makeComponent(VisualFormComponent $component): \Filament\Forms\Components\TextInput
    {
        $props = collect($component->getProps());
        $control = \Filament\Forms\Components\TextInput::make($component->getAttribute('name'));
        if ($props->has('autocapitalize')) {
            $control->autocapitalize($props->get('autocapitalize'));
        }
        if ($props->has('autocomplete')) {
            $control->autocomplete($props->get('autocomplete'));
        }
        if ($props->get('length')) {
            $control->length($props->get('length'));
        }
        if ($props->get('maxLength')) {
            $control->maxLength($props->get('maxLength'));
        }
        if ($props->get('minLength') && $props->get('minLength') > 0) {
            $control->minLength($props->get('minLength'));
        }

        if ($props->get('readOnly')) {
            $control->readOnly();
        }
        if ($props->get('prefix')) {
            $control->prefix($props->get('prefix'));
        }
        if ($props->get('suffix')) {
            $control->suffix($props->get('suffix'));
        }
        if ($props->get('prefixIcon')) {
            $control->prefixIcon($props->get('prefixIcon'));
        }
        if ($props->get('suffixIcon')) {
            $control->suffixIcon($props->get('suffixIcon'));
        }
        if ($props->get('inlinePrefix')) {
            $control->inlinePrefix();
        }
        if ($props->get('inlineSuffix')) {
            $control->inlineSuffix();
        }
        if ($props->get('prefixIconColor')) {
            $control->prefixIconColor($props->get('prefixIconColor'));
        }
        if ($props->get('suffixIconColor')) {
            $control->suffixIconColor($props->get('suffixIconColor'));
        }
        if ($props->get('datalist')) {
            $control->datalist($props->get('datalist'));
        }
        if ($props->get('extraInputAttributes')) {
            $control->extraInputAttributes($props->get('extraInputAttributes'));
        }
        if ($props->get('inputMode')) {
            $control->inputMode($props->get('inputMode'));
        }

        if ($props->get('placeholder')) {
            $control->placeholder($props->get('placeholder'));
        }
        if ($props->get('step')) {
            $control->step($props->get('step'));
        }
        if ($props->get('currentPassword')) {
            $control->currentPassword();
        }
        if ($props->get('email')) {
            $control->email();
        }
        if ($props->get('integer')) {
            $control->integer();
        }
        if ($props->get('mask')) {
            $control->mask($props->get('mask'));
        }
        if ($props->get('maxValue')) {
            $control->maxValue($props->get('maxValue'));
        }
        if ($props->get('minValue')) {
            $control->minValue($props->get('minValue'));
        }
        if ($props->get('numeric')) {
            $control->numeric();
        }
        if ($props->get('password')) {
            $control->password();
        }
        if ($props->get('revealable')) {
            $control->revealable();
        }
        if ($props->get('tel')) {
            $control->tel();
        }
        if ($props->get('telRegex')) {
            $control->telRegex($props->get('telRegex'));
        }
        if ($props->get('type')) {
            $control->type($props->get('type'));
        }
        if ($props->get('url')) {
            $control->url();
        }

        return $control;
    }
}
