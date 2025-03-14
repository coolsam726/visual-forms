<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms\Get;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

class TextInput extends Component
{
    public function getOptionName(): string
    {
        return __('Text Input');
    }

    public function isLayout(): bool
    {
        return false;
    }

    public function hasChildren(): bool
    {
        return false;
    }

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
            'inputMode' => String_::class,
            // See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#inputmode
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

    /**
     * The schema that will be rendered when creating the VisualFormComponent of this type.
     */
    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Tabs\Tab::make(__('Flags'))
                ->statePath('props')
                ->schema(fn () => [
                    ...collect($this->getSupportedProps())->filter(fn ($type, $name) => $type === Boolean::class)
                        ->map(fn ($type, $name) => \Filament\Forms\Components\Checkbox::make($name))
                        ->toArray(),
                ])->columns(3),
            \Filament\Forms\Components\Tabs\Tab::make(__('Integer Properties'))
                ->statePath('props')
                ->schema(fn () => [
                    ...collect($this->getSupportedProps())->filter(fn ($type, $name) => $type === Integer::class)
                        ->map(fn ($type, $name) => \Filament\Forms\Components\TextInput::make($name)->integer())
                        ->toArray(),
                ])->columns(2),
            \Filament\Forms\Components\Tabs\Tab::make(__('String Properties'))
                ->statePath('props')
                ->schema(fn () => [
                    ...collect($this->getSupportedProps())->filter(fn ($type, $name) => $type === String_::class)
                        ->map(fn ($type, $name) => \Filament\Forms\Components\TextInput::make($name))
                        ->toArray(),
                ])->columns(2),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function makeComponent(): \Filament\Forms\Components\TextInput
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record is required to make a component');
        }
        $control = \Filament\Forms\Components\TextInput::make($record->getAttribute('name'));

        // TODO: Make the text input
        return $control;
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema([
            \Filament\Forms\Components\Fieldset::make(__('Basic Validation'))
                ->columns([
                    'default' => 3,
                    'md' => 4,
                    'xl' => 5,
                    '2xl' => 6,
                ])
                ->statePath('props')
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('required')->default(true),
                    \Filament\Forms\Components\Checkbox::make('email')->label(__('Email'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('numeric')->label(__('Numeric'))
                        ->hidden(fn (Get $get) => $get('email'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('integer')->label(__('Integer'))
                        ->hidden(fn (Get $get) => $get('email'))->default(false),
                ]),
        ]);
    }
}
