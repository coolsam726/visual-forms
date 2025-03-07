<?php

namespace Coolsam\VisualForms;

use Awcodes\TableRepeater\Components\TableRepeater;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormField;
use Filament\Forms\Components;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

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
        if (! $form->is_active) {
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
        /**
         * @var Components\TextInput|Components\Select|Components\Field $control
         */
        $control = match ($field->control_type) {
            default => Components\TextInput::make($field->name),
            ControlTypes::Select->value => Components\Select::make($field->name),
            ControlTypes::Textarea->value => Components\Textarea::make($field->name),
            ControlTypes::TagsInput->value => Components\TagsInput::make($field->name),
            ControlTypes::Radio->value => Components\Radio::make($field->name),
            ControlTypes::Toggle->value => Components\Toggle::make($field->name),
            ControlTypes::CheckboxList->value => Components\CheckboxList::make($field->name),
            ControlTypes::Checkbox->value => Components\Checkbox::make($field->name),
            ControlTypes::FileUpload->value => Components\FileUpload::make($field->name),
            ControlTypes::DatePicker->value => Components\DatePicker::make($field->name),
            ControlTypes::TimePicker->value => Components\TimePicker::make($field->name),
            ControlTypes::DateTimePicker->value => Components\DateTimePicker::make($field->name),
            ControlTypes::RichEditor->value => Components\RichEditor::make($field->name),
            ControlTypes::MarkdownEditor->value => Components\MarkdownEditor::make($field->name),
            ControlTypes::Repeater->value => Components\Repeater::make($field->name),
            ControlTypes::KeyValue->value => Components\KeyValue::make($field->name),
            ControlTypes::ColorPicker->value => Components\ColorPicker::make($field->name),
            ControlTypes::ToggleButtons->value => Components\ToggleButtons::make($field->name),
            ControlTypes::TableRepeater->value => TableRepeater::make($field->name),
            ControlTypes::Hidden->value => Components\Hidden::make($field->name),
        };
        $control->required($field->required)->label($field->label)
            ->disabled($field->disabled)
            ->helperText($field->helper_text);

        if ($field->default_value != null) {
            $control->default($field->default_value);
        }
        if (ControlTypes::hasAutocomplete($field->control_type)) {
            $control->autocomplete($field->autocomplete);
        }

        if (ControlTypes::hasAutocapitalize($field->control_type)) {
            $control->autocapitalize($field->autocapitalize);
        }

        if (ControlTypes::hasOptions($field->control_type)) {
            $options = $this->makeOptions($field);
            $control->options($options);
        }

        if ($field->colspan_full) {
            $control->columnSpanFull();
        } elseif ($field->colspan > 1) {
            $control->columnSpan($field->colspan);
        }

        // Handle unique
        if ($field->unique) {
            $rule = Rule::unique(\Config::get('visual-forms.tables.visual_form_entries'), 'payload->' . $field->name);
            if ($field->id) {
                $rule = $rule->ignore($field->id);
            }
            $control->rule($rule);
            //            $control->unique($field->unique, str(\Config::get('visual-forms.tables.visual_form_entries'))->append(',payload->')->append($field->name)->toString());
        }

        if (ControlTypes::hasSearchable($field->control_type)) {
            $control->searchable($field->searchable);
        }

        if (ControlTypes::hasPlaceholder($field->control_type)) {
            $control->placeholder($field->placeholder);
        }

        if (ControlTypes::hasAutofocus($field->control_type)) {
            $control->autofocus($field->autofocus);
        }

        if (ControlTypes::hasReadonly($field->control_type)) {
            $control->readonly($field->readonly);
        }

        if (ControlTypes::hasPrefixAndSuffix($field->control_type)) {
            if ($field->prefix_icon) {
                $control->prefixIcon($field->prefix_icon)->inlinePrefix($field->inline_prefix);
            }

            if ($field->suffix_icon) {
                $control->suffixIcon($field->suffix_icon)->inlineSuffix($field->inline_suffix);
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
        if (! ($field->validation_rules && count($field->validation_rules))) {
            return [];
        }
        $rules = collect($field->validation_rules);

        return $rules->mapWithKeys(fn (
            $rule
        ) => [$rule['rule'] => $rule['rule'] ? "{$rule['rule']}:{$rule['value']}" : $rule['value']])->values()->toArray();
    }

    public function makeOptions(VisualFormField $field): Collection | array | null
    {
        if (! ControlTypes::hasOptions($field->control_type)) {
            return null;
        }

        if ($field->options_from_db) {
            $table = $field->options_db_table;
            if (! $table) {
                return collect();
            }
            $query = \DB::table($table);
            $conditions = $field->options_where_conditions;
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
            if ($field->options_order_by) {
                $query->orderBy($field->options_order_by, $field->options_order_direction);
            }
            $records = $query->get();

            return $records
                ->mapWithKeys(fn ($record) => [
                    $record->{$field->options_key_attribute} => $record->{$field->options_value_attribute},
                ]);
        } else {
            return collect($field->options)->mapWithKeys(fn ($option) => [$option['value'] => $option['label']]);
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
