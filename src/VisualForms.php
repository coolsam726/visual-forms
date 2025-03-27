<?php

namespace Coolsam\VisualForms;

use Coolsam\VisualForms\ComponentTypes\Component;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Forms\Components;
use File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Symfony\Component\Finder\SplFileInfo;

class VisualForms
{
    public function getComponentTypeOptions(): Collection
    {
        $files = File::allFiles(__DIR__ . '/ComponentTypes');

        return collect($files)
            ->map(fn (SplFileInfo $file) => Utils::getFileNamespace($file, 'Coolsam\VisualForms\ComponentTypes'))
            ->filter(fn ($class): bool => is_subclass_of($class, Component::class))
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
        ])->mapWithKeys(fn ($rule) => [$rule => $rule]);
    }

    public function schema(VisualForm $form, bool $editable = false): array
    {
        // if the form is not active, render a placeholder to show that error
        if (! $form->getAttribute('is_active')) {
            return [
                Components\Placeholder::make('form_inactive')
                    ->label(new HtmlString("<h3 class='!text-danger-500'>Form Inactive</h3>"))
                    ->content(new HtmlString("<p class='text-danger'>This form is currently inactive. Please activate the form to view the fields.</p>")),
            ];
        }

        return $form->children()
            ->where('is_active', true)->whereNull('parent_id')
            ->orderBy('sort_order')->get()->map(fn (
                VisualFormComponent $field
            ) => $field->makeComponent($editable))->toArray();
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
            'ip_address' => request()->ip(),
            'is_processed' => $isProcessed,
        ]);
    }
}
