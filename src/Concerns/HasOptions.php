<?php

namespace Coolsam\VisualForms\Concerns;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Utils;
use Filament\Forms;
use Illuminate\Support\Collection;

/**
 * @mixin Forms\Components\Field
 */
trait HasOptions
{
    public function extendOptionsSchema(array $schema = []): array
    {
        return [
            Forms\Components\Fieldset::make(__('Options Configuration'))
                ->statePath('props')->schema([
                    Forms\Components\ToggleButtons::make('optionsSource')
                        ->required()
                        ->options([
                            'static' => __('Static (Define manually)'),
                            'closure' => __('Closure'),
                            'database' => __('Database'),
                            'jsonApi' => __('Json API'),
                        ])->live()->inline()->columnSpanFull(),
                    TableRepeater::make('options')->headers([
                        Header::make(__('Value')),
                        Header::make(__('Label')),
                        Header::make('Should Specify?'),
                    ])->schema([
                        Forms\Components\TextInput::make('value')->required()->autofocus(),
                        Forms\Components\TextInput::make('label'),
                        Forms\Components\ToggleButtons::make('should_specify')->boolean()->inline()->default(false),
                    ])
                        ->columnSpanFull()
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'static')
                        ->disabled(fn (Forms\Get $get) => ($get('optionsSource') !== 'static'))
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'static'),
                    Forms\Components\Select::make('optionsClosure')->label(__('Closure'))
                        ->helperText(__('Closure to use for the options'))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'closure')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'closure')
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'closure')
                        ->prefixIcon('heroicon-o-variable')
                        ->inlinePrefix()
                        ->options(fn () => collect(\Config::get('visual-forms.closures', []))->keys()
                            ->mapWithKeys(fn ($closure) => [$closure => str($closure)->camel()->toString()])),
                    Forms\Components\Select::make('optionsTable')
                        ->options(fn () => VisualForms::getDatabaseTables())
                        ->live()
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'database')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'database')
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'database'),
                    Forms\Components\Select::make('optionsLabelField')->label(__('Label Field'))
                        ->options(fn (Forms\Get $get) => VisualForms::getDatabaseColumns($get('optionsTable')))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'database')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'database' || ! $get('optionsTable'))
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'database' || ! $get('optionsTable')),
                    Forms\Components\Select::make('optionsValueField')->label(__('Value Field'))
                        ->options(fn (Forms\Get $get) => VisualForms::getDatabaseColumns($get('optionsTable')))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'database')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'database' || ! $get('optionsTable'))
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'database' || ! $get('optionsTable')),
                    TableRepeater::make('optionsConditions')
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->visible(fn (Forms\Get $get) => $get('optionsSource') === 'database' && $get('optionsTable'))
                        ->headers([
                            Header::make('or')->label(__('OR?')),
                            Header::make('column'),
                            Header::make('operator'),
                            Header::make('value'),
                        ])
                        ->schema([
                            Forms\Components\Checkbox::make('or')->default(false),
                            Forms\Components\Select::make('column')
                                ->required()
                                ->options(fn (
                                    Forms\Get $get
                                ) => VisualForms::getDatabaseColumns($get('../../optionsTable'))),
                            Forms\Components\Select::make('operator')
                                ->searchable()
                                ->required()
                                ->options(fn () => VisualForms::getDbOperators()),
                            Forms\Components\TextInput::make('value')->required(),
                        ]),
                    Forms\Components\TextInput::make('optionsJsonUrl')->label(__('JSON URL'))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'jsonApi')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi')
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi'),
                    Forms\Components\TextInput::make('optionsLabelField')->label(__('Label Field'))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'jsonApi')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi')
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi'),
                    Forms\Components\TextInput::make('optionsValueField')->label(__('Value Field'))
                        ->required(fn (Forms\Get $get) => $get('optionsSource') === 'jsonApi')
                        ->disabled(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi')
                        ->hidden(fn (Forms\Get $get) => $get('optionsSource') !== 'jsonApi'),
                    ...$schema,
                ]),
        ];
    }

    public function getOptions(): Collection | \Closure
    {
        $props = $this->getProps();

        $labelField = $props->get('optionsLabelField');
        $valueField = $props->get('optionsValueField');

        if ($props->get('optionsSource') === 'static') {
            $labelField = 'label';
            $valueField = 'value';
            $options = collect($props->get('options', []));

            return $options->mapWithKeys(fn ($option) => [$option[$valueField] => $option[$labelField]]);
        } elseif ($props->get('optionsSource') === 'database') {
            $table = $props->get('optionsTable');
            $conditions = $props->get('optionsConditions', []);
            $orderBy = $props->get('optionsOrderBy');
            $orderByDir = $props->get('optionsOrderByDirection');
            if (! $table) {
                return collect();
            }
            $query = \DB::table($table);
            if ($conditions && count($conditions)) {
                $i = 0;
                foreach ($conditions as $condition) {
                    if ($i === 0) {
                        $query->where($condition['column'], $condition['operator'], $condition['value']);
                    } else {
                        // Check if the condition is an OR condition
                        if (isset($condition['or']) && Utils::getBool($condition['or'])) {
                            $query->orWhere($condition['column'], $condition['operator'], $condition['value']);
                        } else {
                            $query->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    }
                    $i++;
                }
            }
            if ($orderBy) {
                $query->orderBy($orderBy, $orderByDir);
            }
            $records = $query->get();

            return $records->mapWithKeys(fn ($record) => [$record->{$valueField} => $record->{$labelField}]);
        } elseif ($props->get('optionsSource') === 'jsonApi') {
            $url = $props->get('optionsJsonUrl');
            $labelField = $props->get('optionsLabelField', 'label');
            $valueField = $props->get('optionsValueField', 'value');

            try {
                $response = \Http::get($url);
                $data = $response->json();

                return collect($data)->mapWithKeys(fn ($item) => [$item[$valueField] => $item[$labelField]]);
            } catch (\Exception $e) {
                \Log::error($e);

                return collect();
            }
        } elseif ($props->get('optionsSource') === 'closure') {
            $closureName = $props->get('optionsClosure');
            $closure = \Config::get("visual-forms.closures.$closureName");
            if (filled($closure) && is_callable($closure)) {
                return $closure;
            } else {
                return collect();
            }
        } else {
            return collect();
        }
    }

    public function makeOptions(&$component): void
    {
        $component->options($this->getOptions());
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);
        $this->makeOptions($component);
    }
}
