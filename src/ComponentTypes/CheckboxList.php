<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Concerns\HasOptions;
use Coolsam\VisualForms\Utils;

class CheckboxList extends Component
{
    use HasOptions;

    public function getOptionName(): string
    {
        return __('Checkbox List');
    }

    public function isLayout(): bool
    {
        return false;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function makeComponent()
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }

        $component = \Filament\Forms\Components\CheckboxList::make($record->getAttribute('name'));
        if ($record->getAttribute('label')) {
            $component->label($record->getAttribute('label'));
        }
        if ($record->getAttribute('helperText')) {
            $component->helperText($record->getAttribute('helperText'));
        }
        $this->makeColumns($component);
        $this->makeUnique($component);
        $this->makeOptions($component);

        $props = $this->getProps();

        if ($props->get('hint')) {
            $component->hint($props->get('hint'));
            if ($props->get('hintIcon')) {
                $component->hintIcon($props->get('hintIcon'));
            }
        }

        if ($props->get('default') !== null) {
            $component->default($props->get('default'));
        }

        if (Utils::getBool($props->get('disabled'))) {
            $component->disabled();
        }

        if (Utils::getBool($props->get('inlineLabel'))) {
            $component->inlineLabel();
        }

        if (Utils::getBool($props->get('required'))) {
            $component->required();
        }

        if (Utils::getBool($props->get('searchable'))) {
            $component->searchable();
        }

        if (Utils::getBool($props->get('unique'))) {
            $component->unique();
        }

        if (Utils::getBool($props->get('inlineLabel'))) {
            $component->inlineLabel();
        }



        $this->makeValidation($component);

        return $component;
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Fieldset::make(__('Checkbox List Properties'))
                ->columns([
                    'md' => 2,
                    'xl' => 3,
                ])
                ->statePath('props')->schema([
                    \Filament\Forms\Components\TextInput::make('helperText')->live(debounce: 1000)
                        ->label(__('Helper Text')),
                    \Filament\Forms\Components\TextInput::make('hint')->live(debounce: 1000)
                        ->label(__('Hint')),
                    \Filament\Forms\Components\Select::make('hintIcon')->label(__('Hint icon'))->options(Utils::getHeroicons())->searchable(),
                    \Filament\Forms\Components\ToggleButtons::make('default')
                        ->inline()
                        ->live()
                        ->label(__('Default State'))
                        ->default(false)
                        ->options([
                            null => __('None'),
                            true => __('True'),
                            false => __('False'),
                        ]),
                    \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('inlineLabel')->label(__('Inline Label'))->live()->hidden(fn ($get) => $get('inline'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('searchable')->label(__('Searchable'))->default(true),
                ]),

        ]);
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema([
            \Filament\Forms\Components\Fieldset::make(__('Checkbox List Validation'))
                ->statePath('props')
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('required')->label(__('Required'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('unique')->label(__('Unique'))->default(false),
                ]),
        ]);
    }

    public function getColumnsSchema(): array
    {
        return $this->extendColumnsSchema();
    }
}
