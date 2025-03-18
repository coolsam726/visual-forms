<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Exception;
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

    /**
     * The schema that will be rendered when creating the VisualFormComponent of this type.
     */
    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Fieldset::make(__('Text Specific Properties'))
                ->statePath('props')
                ->schema(fn () => [
                    \Filament\Forms\Components\TextInput::make('placeholder')->label(__('Placeholder'))->autocapitalize(),
                    \Filament\Forms\Components\TextInput::make('helper_text')->label(__('Helper Text')),
                    \Filament\Forms\Components\TextInput::make('hint')->label(__('Hint')),
                    \Filament\Forms\Components\Checkbox::make('autocapitalize')->label(__('Autocapitalize')),
                    \Filament\Forms\Components\Checkbox::make('autocomplete')->label(__('Autocomplete')),
                ])->columns(3),
            ...$this->affixesSchema(),
        ]);
    }

    public function getColumnsSchema(): array
    {
        return $this->extendColumnsSchema();
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema([
            \Filament\Forms\Components\Fieldset::make(__('Basic Validation'))
                ->columns([
                    'default' => 3,
                    'md' => 3,
                    'xl' => 4,
                    '2xl' => 6,
                ])
                ->statePath('props')
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('required')->live()->default(true),
                    \Filament\Forms\Components\Checkbox::make('unique')->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('readOnly')->label(__('Read Only'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('email')->label(__('Email'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('numeric')->label(__('Numeric'))
                        ->hidden(fn (Get $get) => $get('email'))->default(false)->live(),
                    \Filament\Forms\Components\Checkbox::make('integer')->label(__('Integer'))->live()
                        ->hidden(fn (Get $get) => $get('email'))->default(false)->live(),
                    \Filament\Forms\Components\Checkbox::make('password')->label(__('Password'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('revealable')->label(__('Revealable'))->default(false)
                        ->visible(fn (Get $get) => $get('password')),
                    \Filament\Forms\Components\Checkbox::make('tel')->label(__('Telephone'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('url')->label(__('URL'))->live()->default(false),
                ]),
            \Filament\Forms\Components\Fieldset::make(__('Text Properties'))
                ->statePath('props')
                ->columns([
                    'default' => 3,
                    'md' => 3,
                    'xl' => 4,
                    '2xl' => 5,
                ])
                ->schema([
                    \Filament\Forms\Components\TextInput::make('length')->numeric()->live()->label(__('Length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('minLength')->label(__('Minimum Length'))
                        ->integer()
                        ->live()
                        ->disabled(fn (Get $get) => (bool) $get('length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('maxLength')->label(__('Maximum Length'))
                        ->integer()
                        ->live()
                        ->gte(fn (Get $get) => $get('minLength'))
                        ->disabled(fn (Get $get) => (bool) $get('length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('same')->label(__('Same as'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g confirm_password'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('mask')->label(__('Mask'))
                        ->live(),
                ]),
            \Filament\Forms\Components\Fieldset::make(__('Numeric Comparisons'))
                ->statePath('props')
                ->columns(['md' => 2, 'xl' => 3])
                ->hidden(fn (Get $get) => ! $get('props.numeric'))
                ->schema([
                    \Filament\Forms\Components\TextInput::make('gt')->label(__('Greater Than'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->prefix('>')
                        ->placeholder(__('e.g purchase_price'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('lt')->label(__('Less Than'))
                        ->prefix('<')
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g selling_price'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('gte')
                        ->prefix('>=')
                        ->label(__('Greater than or Equal to'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g selling_price'))->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('lte')
                        ->prefix('<=')
                        ->label(__('Less than or Equal to'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g purchase_price'))->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('minValue')->label(__('Minimum Value'))
                        ->integer()
                        ->live()
                        ->visible(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('maxValue')->label(__('Maximum Value'))
                        ->integer()
                        ->live()
                        ->hidden(fn (Get $get) => $get('email') || ! $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('step')->label(__('Step'))
                        ->numeric()
                        ->live()
                        ->hidden(fn (Get $get) => ! $get('numeric')),
                ]),
        ]);
    }

    /**
     * @throws Exception
     */
    public function makeComponent(): \Filament\Forms\Components\TextInput
    {
        /**
         * 'autocapitalize' => Boolean::class,
         * 'autocomplete' => Boolean::class,
         * 'length' => Integer::class,
         * 'maxLength' => Integer::class,
         * 'minLength' => Integer::class,
         * 'readOnly' => Boolean::class,
         * 'disabled' => Boolean::class,
         * 'prefix' => String_::class,
         * 'suffix' => String_::class,
         * 'prefixIcon' => String_::class,
         * 'suffixIcon' => String_::class,
         * 'inlinePrefix' => Boolean::class,
         * 'inlineSuffix' => Boolean::class,
         * 'prefixIconColor' => String_::class,
         * 'suffixIconColor' => String_::class,
         * 'datalist' => Array_::class,
         * 'extraInputAttributes' => Array_::class,
         * 'inputMode' => String_::class,
         * // See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#inputmode
         * 'placeholder' => String_::class,
         * 'step' => Integer::class,
         * 'currentPassword' => Boolean::class,
         * 'email' => Boolean::class,
         * 'integer' => Boolean::class,
         * 'mask' => String_::class,
         * 'maxValue' => Integer::class,
         * 'minValue' => Integer::class,
         * 'numeric' => Boolean::class,
         * 'password' => Boolean::class,
         * 'revealable' => Boolean::class,
         * 'tel' => Boolean::class,
         * 'telRegex' => String_::class,
         * 'type' => String_::class,
         * 'url' => Boolean::class,
         */
        $record = $this->getRecord();
        if (! $record) {
            throw new Exception('Record is required to make a component');
        }
        $control = \Filament\Forms\Components\TextInput::make($record->getAttribute('name'));
        // Common props
        if ($label = $record->getAttribute('label')) {
            $control->label($label);
        }

        if ($helperText = $record->getAttribute('helper_text')) {
            $control->helperText($helperText);
        }

        if ($hint = $record->getAttribute('hint')) {
            $control->hint($hint);
        }

        $this->makeColumns($control);
        $this->makeValidation($control);
        $props = collect($record->getAttribute('props') ?? []);
        if ($props->isEmpty()) {
            return $control;
        }

        if (Utils::getBool($props->get('required'))) {
            $control->required();
        }

        if ($props->has('autocapitalize')) {
            $control->autocapitalize(Utils::getBool($props->get('autocapitalize')));
        }

        if ($props->has('autocomplete')) {
            $control->autocomplete(Utils::getBool($props->get('autocomplete')));
        }

        if ($props->get('maxLength')) {
            $control->maxLength(intval($props->get('maxLength')));
        }

        if ($props->get('minLength')) {
            $control->minLength(intval($props->get('minLength')));
        }

        if ($props->get('length')) {
            $control->length(intval($props->get('length')));
        }

        if ($props->has('readOnly')) {
            $control->readOnly(Utils::getBool($props->get('readOnly')));
        }

        $this->makeAffixes($control);

        if ($props->get('datalist')) {
            $control->datalist(collect($props->get('datalist'))->toArray());
        }

        if ($props->get('extraInputAttributes')) {
            $control->extraInputAttributes(collect($props->get('extraInputAttributes'))->toArray());
        }

        if ($props->get('inputMode')) {
            $control->inputMode($props->get('inputMode'));
        }

        if ($props->get('placeholder')) {
            $control->placeholder($props->get('placeholder'));
        }

        if ($props->get('step')) {
            $control->step(intval($props->get('step')));
        }

        if (Utils::getBool($props->get('currentPassword'))) {
            $control->currentPassword();
        }

        if (Utils::getBool($props->get('password'))) {
            $control->password();
        }

        if (Utils::getBool($props->get('revealable'))) {
            $control->revealable();
        }

        if ($props->has('email') && Utils::getBool($props->get('email'))) {
            $control->email();
        } elseif ($props->has('numeric') && Utils::getBool($props->get('numeric'))) {
            $control->numeric();

            if ($props->get('maxValue')) {
                $control->maxValue(intval($props->get('maxValue')));
            }

            if ($props->get('minValue')) {
                $control->minValue(intval($props->get('minValue')));
            }
        }

        if ($props->has('integer') && Utils::getBool($props->get('integer'))) {
            $control->integer();
        }

        if ($props->get('mask')) {
            $control->mask($props->get('mask'));
        }

        if ($props->get('type')) {
            $control->type($props->get('type'));
        }

        if (Utils::getBool($props->get('tel'))) {
            $control->tel();
        }

        if ($props->get('telRegex')) {
            $control->telRegex($props->get('telRegex'));
        }

        if (Utils::getBool($props->get('url'))) {
            $control->url();
        }

        if ($props->get('gt')) {
            $control->gt($props->get('gt'));
        }

        if ($props->get('lt')) {
            $control->lt($props->get('lt'));
        }

        if ($props->get('gte')) {
            $control->gte($props->get('gte'));
        }

        if ($props->get('lte')) {
            $control->lte($props->get('lte'));
        }

        if ($props->get('same')) {
            $control->same($props->get('same'));
        }

        if ($props->get('unique')) {
            $this->makeUnique($control);
        }

        return $control;
    }
}
