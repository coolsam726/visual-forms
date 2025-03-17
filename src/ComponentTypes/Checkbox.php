<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;

class Checkbox extends Component
{
    public function getOptionName(): string
    {
        return __('Checkbox');
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

        $component = \Filament\Forms\Components\Checkbox::make($record->getAttribute('name'));
        if ($record->getAttribute('label')) {
            $component->label($record->getAttribute('label'));
        }
        if ($record->getAttribute('helperText')) {
            $component->helperText($record->getAttribute('helperText'));
        }
        $this->makeColumns($component);
        $this->makeUnique($component);
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

        if ($props->get('disabled')) {
            $component->disabled();
        }

        if ($props->get('inline')) {
            $component->inline();
        } elseif ($props->get('inlineLabel')) {
            $component->inlineLabel();
        }

        if (Utils::getBool($props->get('required'))) {
            $component->required();
        }

        if (Utils::getBool($props->get('accepted'))) {
            $component->accepted();
        }

        if (Utils::getBool($props->get('confirmed'))) {
            $component->confirmed();
        }

        if (Utils::getBool($props->get('unique'))) {
            $component->unique();
        }

        if ($props->get('gt')) {
            $component->gt($props->get('gt'));
        } elseif ($props->get('gte')) {
            $component->gte($props->get('gte'));
        }

        if ($props->get('lt')) {
            $component->lt($props->get('lt'));
        }

        if ($props->get('lte')) {
            $component->lte($props->get('lte'));
        }

        if ($props->get('same')) {
            $component->same($props->get('same'));
        }

        $this->makeValidation($component);

        return $component;
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Fieldset::make(__('Checkbox Properties'))
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
                    \Filament\Forms\Components\Checkbox::make('inline')->label(__('Inline'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('inlineLabel')->label(__('Inline Label'))->live()->hidden(fn ($get) => $get('inline'))->default(false),
                ]),
        ]);
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema([
            \Filament\Forms\Components\Fieldset::make(__('Checkbox Validation'))
                ->statePath('props')
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('required')->label(__('Required'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('accepted')->label(__('Accepted'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('confirmed')->label(__('Confirmed'))->default(false),
                    \Filament\Forms\Components\Checkbox::make('unique')->label(__('Unique'))->default(false),
                    \Filament\Forms\Components\TextInput::make('gt')->numeric()->integer()->label(__('Greater Than')),
                    \Filament\Forms\Components\TextInput::make('gte')->numeric()->integer()->label(__('Greater Than or Equal')),
                    \Filament\Forms\Components\TextInput::make('lt')->numeric()->integer()->label(__('Less Than')),
                    \Filament\Forms\Components\TextInput::make('lte')->numeric()->integer()->label(__('Less Than or Equal')),
                    \Filament\Forms\Components\TextInput::make('same')->label(__('Same as')),
                ]),
        ]);
    }

    public function getColumnsSchema(): array
    {
        return $this->extendColumnsSchema([
        ]);
    }
}
