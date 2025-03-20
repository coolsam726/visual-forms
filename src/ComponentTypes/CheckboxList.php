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

        $this->configureComponent($component);

        return $component;
    }

    protected function configureComponent(&$component): void
    {
        $record = $this->getRecord();
        if ($record->getAttribute('label')) {
            $component->label($record->getAttribute('label'));
        }
        $this->makeColumns($component);
        $this->makeUnique($component);
        $this->makeOptions($component);
        $this->makeStatePath($component);
        
        $props = $this->getProps();

        if ($helper = $props->get('helperText')) {
            $component->helperText($helper);
        }

        if ($props->get('hint')) {
            $component->hint($props->get('hint'));
        }
        if ($props->get('hintIcon')) {
            $component->hintIcon($props->get('hintIcon'));
        }

        if ($props->get('default') !== null) {
            $component->default($props->get('default'));
        }

        if (Utils::getBool($props->get('disabled'))) {
            $component->disabled();
        }
        $component->inlineLabel(Utils::getBool($props->get('inlineLabel')));

        if (Utils::getBool($props->get('required'))) {
            $component->required();
        }

        if (Utils::getBool($props->get('searchable'))) {
            // check if compoenent has searchable method
            if (method_exists($component, 'searchable')) {
                $component->searchable();
            }
        }

        if (Utils::getBool($props->get('unique'))) {
            $component->unique();
        }

        if (Utils::getBool($props->get('inline'))) {
            if (method_exists($component, 'inline')) {
                $component->inline();
            }
        }
        if (Utils::getBool($props->get('inlineLabel'))) {
            $component->inlineLabel();
        }

        $this->makeValidation($component);
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema(
            [
                \Filament\Forms\Components\Fieldset::make(__($this->getOptionName() . ' Properties'))
                    ->columns([
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->statePath('props')->schema(
                        $this->getMainSchemaFields()
                    ),

            ]
        );
    }

    protected function getMainSchemaFields(): array
    {
        return [
            \Filament\Forms\Components\TextInput::make('helperText')->live(debounce: 1000)
                ->label(__('Helper Text')),
            \Filament\Forms\Components\TextInput::make('hint')->live(debounce: 1000)
                ->label(__('Hint')),
            \Filament\Forms\Components\Select::make('hintIcon')->label(__('Hint icon'))->options(Utils::getHeroicons())->searchable(),
            \Filament\Forms\Components\TextInput::make('default')
                ->live()
                ->label(__('Default State'))
                ->default(false),
            \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->default(false),
            \Filament\Forms\Components\Checkbox::make('inline')->label(__('Inline'))->live(),
            \Filament\Forms\Components\Checkbox::make('inlineLabel')->label(__('Inline Label'))->live(),
            \Filament\Forms\Components\Checkbox::make('searchable')->label(__('Searchable'))->default(true),
        ];
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
