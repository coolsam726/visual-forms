<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms;
use Illuminate\Support\Collection;

class TagsInput extends Field
{
    public function getOptionName(): string
    {
        return __('Tags Input');
    }

    public function letThereBe(string $name): Forms\Components\Component | Forms\Components\TagsInput
    {
        return Forms\Components\TagsInput::make($name);
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            ...parent::getSpecificBasicSchema(),
            Forms\Components\Fieldset::make()->schema([
                Forms\Components\TagsInput::make('suggestions')->label(__('Suggestions'))
                    ->placeholder(__('Suggestions'))
                    ->helperText(__('Suggestions for the tags input field')),
                Forms\Components\Select::make('separator')->label(__('Separator'))
                    ->placeholder(__('Separator'))
                    ->searchable()
                    ->options($this->getKeyboardKeys())
                    ->hint('leave empty to store the tags  as a json array')
                    ->helperText(__('Separator for the tags input field, if you don\'t want to store the tags as a JSON')),
                Forms\Components\Select::make('splitKeys')->label(__('Split Keys'))
                    ->options($this->getKeyboardKeys())
                    ->multiple()
                    ->helperText(__('Split keys allow you to map specific buttons on your user’s keyboard to create a new tag. The default one is Enter')),
                Forms\Components\TextInput::make('tagPrefix')->label(__('Tag Prefix'))
                    ->placeholder(__('Tag Prefix'))
                    ->helperText(__('Tag prefix for the tags input field')),
                Forms\Components\TextInput::make('tagSuffix')->label(__('Tag Suffix'))
                    ->placeholder(__('Tag Suffix'))
                    ->helperText(__('Tag suffix for the tags input field')),
                Forms\Components\ToggleButtons::make('reorderable')
                    ->boolean()->inline()
                    ->label(__('Reorderable'))
                    ->helperText(__('Allow the user to reorder the tags')),
                Forms\Components\ToggleButtons::make('color')
                    ->inline()
                    ->options(Utils::getAppColors())
                    ->colors(fn () => collect(Utils::getAppColors())->keys()->mapWithKeys(fn ($color) => [$color => $color]))
                    ->label(__('Tag Color'))
                    ->helperText(__('Color of the tags')),
            ])->columnSpanFull()->columns([
                'lg' => 2,
            ]),
        ];
    }

    public function getSpecificValidationSchema(): array
    {
        return [
            ...parent::getSpecificValidationSchema(),
            Forms\Components\Section::make('Tag Validation')
                ->description('You may add validation rules for each tag separately by adding the rules as tags below')
                ->schema([
                    Forms\Components\TagsInput::make('nestedRecursiveRules')
                        ->label(__('Nested Recursive Rules'))
                        ->placeholder(__('required, email, min:3'))
                        ->helperText(__('Nested recursive rules for the tags input field')),
                ])->columnSpanFull(),
        ];
    }

    public function getKeyboardKeys(): Collection
    {
        return collect([
            'Tab' => __('Tab'),
            '\u0020' => __('Space ( )'),
            '|' => __('Pipe (|)'),
            ',' => __('Comma (,)'),
            ';' => __('Semicolon (;)'),
            '/' => __('Slash (/)'),
            '\\' => __("Backslash (\)"),
            '-' => __('Dash (-)'),
            '_' => __('Underscore (_)'),
            '.' => __('Dot (.)'),
            ':' => __('Colon (:)'),
            '!' => __('Exclamation mark (!)'),
            '?' => __('Question mark (?)'),
            '@' => __('At (@)'),
            '#' => __('Hash (#)'),
            '$' => __('Dollar ($)'),
            '%' => __('Percent (%)'),
            '^' => __('Caret (^)'),
            '&' => __('Ampersand (&)'),
            '*' => __('Asterisk (*)'),
        ]);
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);
        $props = $this->getProps();

        if (filled($props->get('separator')) && method_exists($component, 'separator')) {
            $separator = str($props->get('separator'))->replace('\u0020', '')->toString();
            $component->separator($separator);
        }

        if (filled($props->get('suggestions')) && method_exists($component, 'suggestions')) {
            $component->suggestions($props->get('suggestions'));
        }

        if (filled($props->get('splitKeys')) && method_exists($component, 'splitKeys')) {
            $keys = collect($props->get('splitKeys'))
                ->map(
                    fn ($key) => str($key)
                        ->replace('\u0020', '')->toString()
                )->toArray();
            $component->splitKeys($keys);
        }

        if (filled($props->get('tagPrefix')) && method_exists($component, 'tagPrefix')) {
            $component->tagPrefix($props->get('tagPrefix'));
        }

        if (filled($props->get('tagSuffix')) && method_exists($component, 'tagSuffix')) {
            $component->tagSuffix($props->get('tagSuffix'));
        }

        if (filled($props->get('reorderable')) && method_exists($component, 'reorderable')) {
            $component->reorderable(Utils::getBool($props->get('reorderable')));
        }

        if (filled($props->get('color')) && method_exists($component, 'color')) {
            $component->color($props->get('color'));
        }

        if (filled($props->get('nestedRecursiveRules')) && method_exists($component, 'nestedRecursiveRules')) {
            $component->nestedRecursiveRules($props->get('nestedRecursiveRules'));
        }
    }
}
