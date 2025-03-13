<?php

namespace Coolsam\VisualForms;

use Awcodes\TableRepeater\Components\TableRepeater;
use Coolsam\VisualForms\ComponentTypes\Component;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormField;
use Filament\Forms\Components;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Symfony\Component\Finder\SplFileInfo;

class VisualForms
{
    // Enum of control types
    public function getControlTypeOptions(): Collection
    {
        // get options from ControlTypes enum, as a key value array
        return collect(ControlTypes::cases())
            ->pluck('value', 'name')
            ->mapWithKeys(fn ($value, $key) => [
                $key => str($value)->camel()->snake()->title()->explode('_')->join(' '),
            ]);
    }

    public function getComponentTypeOptions(): Collection
    {
        $files = \File::allFiles(__DIR__ . '/ComponentTypes');

        return collect($files)
            ->map(fn (SplFileInfo $file) => Utils::getFileNamespace($file, 'Coolsam\VisualForms\ComponentTypes'))
            ->filter(fn ($class) => is_subclass_of($class, Component::class))
            ->mapWithKeys(fn (string $class) => [
                $class => class_exists($class) ? (new $class)->getOptionName() : str($class)->afterLast('\\')->camel()->snake()->title()->explode('_')->join(' '),
            ]);
    }

    public function getValidationRules(): Collection
    {
        return collect([
            'accepted',
            'accepted_if',
            'active_url',
            'after',
            'after_or_equal',
            'alpha',
            'alpha_dash',
            'alpha_num',
            'array',
            'ascii',
            'bail',
            'before',
            'before_or_equal',
            'between',
            'boolean',
            'confirmed',
            'current_password',
            'date',
            'date_equals',
            'date_format',
            'decimal',
            'declined',
            'declined_if',
            'different',
            'digits',
            'digits_between',
            'dimensions',
            'distinct',
            'doesnt_end_with',
            'doesnt_start_with',
            'email',
            'ends_with',
            'enum',
            'exists',
            'file',
            'filled',
            'gt',
            'gte',
            'image',
            'in',
            'in_array',
            'integer',
            'ip',
            'ipv4',
            'ipv6',
            'json',
            'lowercase',
            'lt',
            'lte',
            'mac_address',
            'max',
            'max_digits',
            'mimes',
            'mimetypes',
            'min',
            'min_digits',
            'missing',
            'missing_if',
            'missing_unless',
            'missing_with',
            'missing_with_all',
            'multiple_of',
            'not_in',
            'not_regex',
            'nullable',
            'numeric',
            'password',
            'present',
            'prohibited',
            'prohibited_if',
            'prohibited_unless',
            'prohibits',
            'regex',
            'required',
            'required_array_keys',
            'required_if',
            'required_if_accepted',
            'required_unless',
            'required_with',
            'required_with_all',
            'required_without',
            'required_without_all',
            'same',
            'size',
            'starts_with',
            'string',
            'timezone',
            'unique',
            'uppercase',
            'ulid',
            'url',
            'uuid',
        ])->mapWithKeys(fn ($rule) => [$rule => str($rule)->camel()->snake()->title()->explode('_')->join(' ')]);
    }

    public function schema(VisualForm $form): array
    {
        // if the form is not active, render a placeholder to show that error
        if (! $form->getAttribute('is_active')) {
            return [
                Components\Placeholder::make('form_inactive')
                    ->label(new HtmlString("<h3 class='!text-danger-500'>Form Inactive</h3>"))
                    ->content(new HtmlString("<p class='text-danger'>This form is currently inactive. Please activate the form to view the fields.</p>")),
            ];
        }

        return $form->fields()->orderBy('sort_order')->get()->map(fn (
            VisualFormField $field
        ) => $this->makeField($field))->toArray();
    }

    public function makeField(VisualFormField $field)
    {
        $control = match ($field->getAttribute(key: 'control_type')) {
            default => Components\TextInput::make($field->getAttribute('name')),
            ControlTypes::Select->value => Components\Select::make($field->getAttribute('name')),
            ControlTypes::Textarea->value => Components\Textarea::make($field->getAttribute('name')),
            ControlTypes::TagsInput->value => Components\TagsInput::make($field->getAttribute('name')),
            ControlTypes::Radio->value => Components\Radio::make($field->getAttribute('name')),
            ControlTypes::Toggle->value => Components\Toggle::make($field->getAttribute('name')),
            ControlTypes::CheckboxList->value => Components\CheckboxList::make($field->getAttribute('name')),
            ControlTypes::Checkbox->value => Components\Checkbox::make($field->getAttribute('name')),
            ControlTypes::FileUpload->value => Components\FileUpload::make($field->getAttribute('name')),
            ControlTypes::DatePicker->value => Components\DatePicker::make($field->getAttribute('name')),
            ControlTypes::TimePicker->value => Components\TimePicker::make($field->getAttribute('name')),
            ControlTypes::DateTimePicker->value => Components\DateTimePicker::make($field->getAttribute('name')),
            ControlTypes::RichEditor->value => Components\RichEditor::make($field->getAttribute('name')),
            ControlTypes::MarkdownEditor->value => Components\MarkdownEditor::make($field->getAttribute('name')),
            ControlTypes::Repeater->value => Components\Repeater::make($field->getAttribute('name')),
            ControlTypes::KeyValue->value => Components\KeyValue::make($field->getAttribute('name')),
            ControlTypes::ColorPicker->value => Components\ColorPicker::make($field->getAttribute('name')),
            ControlTypes::ToggleButtons->value => Components\ToggleButtons::make($field->getAttribute('name')),
            ControlTypes::TableRepeater->value => TableRepeater::make($field->getAttribute('name')),
            ControlTypes::Hidden->value => Components\Hidden::make($field->getAttribute('name')),
        };
        $control->required($field->getAttribute('required'))->label($field->getAttribute('label'))
            ->disabled($field->getAttribute('disabled'))
            ->helperText($field->getAttribute('helper_text'));

        if ($field->getAttribute('default_value') != null) {
            $control->default($field->getAttribute('default_value'));
        }
        if (ControlTypes::hasAutocomplete($field->getAttribute('control_type'))) {
            $control->autocomplete($field->getAttribute('autocomplete'));
        }

        if (ControlTypes::hasAutocapitalize($field->getAttribute('control_type'))) {
            $control->autocapitalize($field->getAttribute('autocapitalize'));
        }

        if (ControlTypes::hasOptions($field->getAttribute('control_type'))) {
            $options = $this->makeOptions($field);
            $control->options($options);
        }

        if ($field->getAttribute('colspan_full')) {
            $control->columnSpanFull();
        } elseif ($field->getAttribute('colspan') > 1) {
            $control->columnSpan($field->getAttribute('colspan'));
        }

        // Handle unique
        if ($field->getAttribute('unique')) {
            $rule = Rule::unique(\Config::get('visual-forms.tables.visual_form_entries'), 'payload->' . $field->getAttribute('name'));
            if ($field->getAttribute('id')) {
                $rule = $rule->ignore($field->getAttribute('id'));
            }
            $control->rule($rule);
        }

        if (ControlTypes::hasSearchable($field->getAttribute('control_type'))) {
            $control->searchable($field->getAttribute('searchable'));
        }

        if (ControlTypes::hasPlaceholder($field->getAttribute('control_type'))) {
            $control->placeholder($field->getAttribute('placeholder'));
        }

        if (ControlTypes::hasAutofocus($field->getAttribute('control_type'))) {
            $control->autofocus($field->getAttribute('autofocus'));
        }

        if (ControlTypes::hasReadonly($field->getAttribute('control_type'))) {
            $control->readonly($field->getAttribute('readonly'));
        }

        if (ControlTypes::hasPrefixAndSuffix($field->getAttribute('control_type'))) {
            if ($field->getAttribute('prefix_icon')) {
                $control->prefixIcon($field->getAttribute('prefix_icon'))->inlinePrefix($field->getAttribute('inline_prefix'));
            }

            if ($field->getAttribute('suffix_icon')) {
                $control->suffixIcon($field->getAttribute('suffix_icon'))->inlineSuffix($field->getAttribute('inline_suffix'));
            }
        }
        $rules = $this->makeRules($field);
        if (count($rules)) {
            $control->rules($rules);
        }

        return $control;
    }

    public function makeRules(VisualFormField $field): array
    {
        if (! ($field->getAttribute('validation_rules') && count($field->getAttribute('validation_rules')))) {
            return [];
        }
        $rules = collect($field->getAttribute('validation_rules'));

        return $rules->mapWithKeys(fn (
            $rule
        ) => [$rule['rule'] => $rule['rule'] ? "{$rule['rule']}:{$rule['value']}" : $rule['value']])->values()->toArray();
    }

    public function makeOptions(VisualFormField $field): Collection | array | null
    {
        if (! ControlTypes::hasOptions($field->getAttribute('control_type'))) {
            return null;
        }

        if ($field->getAttribute('options_from_db')) {
            $table = $field->getAttribute('options_db_table');
            if (! $table) {
                return collect();
            }
            $query = \DB::table($table);
            $conditions = $field->getAttribute('options_where_conditions');
            if ($conditions && count($conditions)) {
                $i = 0;
                foreach ($conditions as $condition) {
                    if ($i === 0) {
                        $query->where($condition['column'], $condition['operator'], $condition['value']);
                    } else {
                        // Check if the condition is an OR condition
                        if (isset($condition['or']) && $condition['or']) {
                            $query->orWhere($condition['column'], $condition['operator'], $condition['value']);
                        } else {
                            $query->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    }
                    $i++;
                }
            }
            if ($field->getAttribute('options_order_by')) {
                $query->orderBy($field->getAttribute('options_order_by'), $field->getAttribute('options_order_direction'));
            }
            $records = $query->get();

            return $records
                ->mapWithKeys(fn ($record) => [
                    $record->{$field->getAttribute('options_key_attribute')} => $record->{$field->getAttribute('options_value_attribute')},
                ]);
        } else {
            return collect($field->getAttribute('options'))->mapWithKeys(fn ($option) => [$option['value'] => $option['label']]);
        }
    }

    public function getDatabaseTables()
    {
        $tables = Schema::getTables();

        return collect($tables)
            ->pluck('name', 'name')
            ->except([
                'migrations',
                'password_resets',
                'password_reset_tokens',
                'sqlite_master',
                'sqlite_sequence',
                'failed_jobs',
                'jobs',
                'job_batches',
                'sessions',
                'telescope_entries',
                'telescope_entries_tags',
                'cache',
                'cache_tags',
                'cache_locks',
            ])
            ->mapWithKeys(fn ($value) => [$value => str($value)->camel()->snake()->title()->explode('_')->join(' ')]);
    }

    public function getDatabaseColumns(string $table)
    {
        $columns = Schema::getColumns($table);

        return collect($columns)
            ->pluck('name', 'name')
            ->except([
                'password',
                'remember_token',
                'secret',
                'secret_key',
                'api_token',
                'api_key',
            ])
            ->mapWithKeys(fn ($value) => [$value => str($value)->camel()->snake()->title()->explode('_')->join(' ')]);
    }

    public function getDbOperators(): array
    {
        return [
            '=' => 'Equals (=)',
            '!=' => 'Not Equals (!=)',
            '<' => 'Less Than (<)',
            '<=' => 'Less Than or Equals (<=)',
            '>' => 'Greater Than (>)',
            '>=' => 'Greater Than or Equals (>=)',
            'like' => 'Like (LIKE)',
            'ilike' => 'ILike (ILIKE)',
            'not' => 'Not (NOT)',
            'in' => 'In (IN)',
            'between' => 'Between (BETWEEN)',
        ];
    }

    public function recordSubmission(VisualForm $record, array $data, bool $isProcessed = false)
    {
        return $record->entries()->create([
            'payload' => $data,
            'ip_address' => request()->getClientIp(),
            'is_processed' => $isProcessed,
        ]);
    }
}
