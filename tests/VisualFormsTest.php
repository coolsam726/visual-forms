<?php

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormField;
use Coolsam\VisualForms\VisualForms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->visualForms = new VisualForms;
    // Create a VisualForm model record
    $this->visualForm = VisualForm::create([
        'name' => 'Contract Us',
        'slug' => 'contact-us',
        'description' => 'Contact us form',
        'is_active' => true,
    ]);
});

it('makes rules', function () {
    $field = new VisualFormField([
        'label' => 'Name',
        'name' => 'name',
        'control_type' => 'text',
        'form_id' => $this->visualForm->id,
    ]);
    $field->save();
    $field->validation_rules = [
        ['rule' => 'required', 'value' => 'true'],
        ['rule' => 'max', 'value' => '255'],
    ];

    $rules = $this->visualForms->makeRules($field);

    expect($rules)->toEqual(['required:true', 'max:255']);
});

it('makes options', function () {
    $field = new VisualFormField;
    $field->name = 'options_field';
    $field->control_type = ControlTypes::Select->value;
    $field->form_id = $this->visualForm->id;
    $field->options = [
        ['value' => '1', 'label' => 'Option 1'],
        ['value' => '2', 'label' => 'Option 2'],
    ];
    $field->save();

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class)
        ->and($options->toArray())->toEqual(['1' => 'Option 1', '2' => 'Option 2']);
});

it('gets database tables', function () {
    Schema::shouldReceive('getTables')->andReturn([
        (object) ['name' => 'users'],
        (object) ['name' => 'posts'],
    ]);

    $tables = $this->visualForms->getDatabaseTables();

    expect($tables->values()->toArray())->toEqual(['Users', 'Posts']);
});

it('gets database columns', function () {
    Schema::shouldReceive('getColumns')->with('users')->andReturn([
        (object) ['name' => 'id'],
        (object) ['name' => 'name'],
    ]);

    $columns = $this->visualForms->getDatabaseColumns('users');

    expect($columns->values()->toArray())->toEqual(['Id', 'Name']);
});

it('gets db operators', function () {
    $operators = $this->visualForms->getDbOperators();

    expect($operators)->toEqual([
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
    ]);
});

it('records submission', function () {
    $record = Mockery::mock(VisualForm::class);
    $record->shouldReceive('entries->create')->andReturn(true);

    $data = ['field1' => 'value1', 'field2' => 'value2'];
    $result = $this->visualForms->recordSubmission($record, $data, true);

    expect($result)->toBeTrue();
});

it('makes a Text Input field', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getAutocapitalize())->toEqual('off')
        ->and($control->getDefaultState())->toEqual('John Doe');
});

it('makes a Text Input field with default value', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe');
});

it('makes a Text Input field with autofocus', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => true,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->isAutofocused())->toBeTrue();
});

it('makes a Text Input field with autocapitalize', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => true,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->getAutocapitalize())->toEqual('on');
});

it('makes a Text Input field with disabled', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => true,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->isDisabled())->toBeTrue();
});

it('makes a Text Input field with readonly', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => true,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->isReadonly())->toBeTrue();
});

it('makes a Select field', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'searchable' => true,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())->toEqual('Status')
        ->and($control)->toBeInstanceOf(Select::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('active')
        ->and($control->isSearchable())->toBeTrue()
        ->and($control->getOptions())->toEqual(['active' => 'Active', 'inactive' => 'Inactive']);
});

it('makes a Radio field', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Radio->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())
        ->toEqual('Status')
        ->and($control)->toBeInstanceOf(Radio::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('active')
        ->and($control->getOptions())->toEqual(['active' => 'Active', 'inactive' => 'Inactive']);
});

it('makes a Radio field with default value', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Radio->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())
        ->toEqual('Status')
        ->and($control)->toBeInstanceOf(Radio::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('active');
});

it('makes a Radio field with autofocus', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Radio->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => true,
        'autocapitalize' => false,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())
        ->toEqual('Status')
        ->and($control)->toBeInstanceOf(Radio::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('active')
        ->and($control->isAutofocused())->toBeTrue();
});

// test that it makes a checkbox field
it('makes a checkbox field', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Checkbox->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())
        ->toEqual('Status')
        ->and($control)->toBeInstanceOf(\Filament\Forms\Components\Checkbox::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('active');
});

// test that it gets control type options
it('gets control type options', function () {
    $options = $this->visualForms->getControlTypeOptions();

    // Test that the options list contains some of the keys and values of the control types
    expect($options)
        ->toBeInstanceOf(Collection::class)
        ->and($options)
        ->toHaveKeys(['TextInput', 'Select', 'Radio', 'Checkbox', 'FileUpload'])
        ->and($options->get('TextInput'))->toEqual('Text Input')
        ->and($options->get('Select'))->toEqual('Select')
        ->and($options->get('Radio'))->toEqual('Radio')
        ->and($options->get('Checkbox'))->toEqual('Checkbox')
        ->and($options->get('FileUpload'))->toEqual('File Upload');
});

// test the schema method
it('makes a schema with given fields', function () {
    // save fields to the form
    $field1 = new VisualFormField([
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);
    $field1->save();

    $field2 = new VisualFormField([
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'searchable' => true,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field2->save();

    $field3 = new VisualFormField([
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Radio->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);

    $field3->save();
    $schema = $this->visualForms->schema($this->visualForm);

    expect($schema)->tobeArray()
        ->and(count($schema))->toEqual(3);
});

// test validation rules
it('makes validation rules', function () {
    $field = new VisualFormField([
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);
    $field->validation_rules = [
        ['rule' => 'required', 'value' => 'true'],
        ['rule' => 'max', 'value' => '255'],
    ];

    $field->save();

    $this->visualForms->makeRules($field);
    $rules = $this->visualForms->getValidationRules();

    $control = $this->visualForms->makeField($field);

    expect($rules)
        ->toBeInstanceOf(Collection::class)
        ->and($rules->toArray())
        ->toHaveKeys(['required', 'max'])
        ->and($rules->get('required'))->toEqual('Required')
        ->and($rules->get('max'))->toEqual('Max')
        ->and($control->getValidationRules())
        ->toBeArray()
        ->and($control->getValidationRules())
        ->toContain('required:true', 'max:255');
});

// test an inactive form
it('makes a schema with inactive form', function () {
    $this->visualForm->is_active = false;
    // Add fields
    $field1 = new VisualFormField([
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);
    $field1->save();

    $field2 = new VisualFormField([
        'label' => 'Status',
        'name' => 'status',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'active',
        'autofocus' => false,
        'autocapitalize' => false,
        'searchable' => true,
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ],
    ]);
    $field2->save();

    $this->visualForm->save();

    $schema = $this->visualForms->schema($this->visualForm);

    expect($schema)->toBeArray()
        ->and(count($schema))
        ->toEqual(1)
        ->and(count($schema))
        ->and($schema[0])
        ->toBeInstanceOf(\Filament\Forms\Components\Placeholder::class);
});

// test that it makes a File Upload field
it('makes a File Upload field', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Avatar',
        'name' => 'avatar',
        'control_type' => ControlTypes::FileUpload->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control->getLabel())
        ->toEqual('Avatar')
        ->and($control)->toBeInstanceOf(\Filament\Forms\Components\FileUpload::class)
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe');
});

it('makes a field with column span', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
        'colspan' => 2,
        'colspan_full' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->getColumnSpan('default'))->toEqual(2);
});

it('makes a field with column span full', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
        'colspan_full' => true,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->getColumnSpan('default'))->toEqual('full');
});

it('makes a field with unique rule', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Email',
        'name' => 'email',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'unique' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    $uniqueRule = (new \Illuminate\Validation\Rules\Unique('visual_form_entries', 'payload->email'))
        ->ignore($this->visualForm->id);

    // get the unique rule from getValidationRules
    $uniqueRuleFromValidationRules = $control->getValidationRules()[1];

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Email')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getValidationRules())->toHaveCount(2)
        ->and($uniqueRuleFromValidationRules)
        ->toEqual($uniqueRule);
});

it('makes a field with prefix and suffix icons', function () {
    $field = new VisualFormField(attributes: [
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
        'prefix_icon' => 'heroicons-o-user',
        'suffix_icon' => 'heroicons-o-check',
        'inline_prefix' => true,
        'inline_suffix' => false,
    ]);

    $field->save();

    $control = $this->visualForms->makeField($field);

    expect($control)->toBeInstanceOf(TextInput::class)
        ->and($control->getLabel())->toEqual('Name')
        ->and($control->isRequired())->toBeTrue()
        ->and($control->getDefaultState())->toEqual('John Doe')
        ->and($control->getPrefixIcon())->toEqual('heroicons-o-user')
        ->and($control->isPrefixInline())->toBeTrue()
        ->and($control->getSuffixIcon())->toEqual('heroicons-o-check')
        ->and($control->isSuffixInline())->toBeFalse();
});

it('returns null for control types without options', function () {
    $field = new VisualFormField([
        'label' => 'Name',
        'name' => 'name',
        'control_type' => ControlTypes::TextInput->value,
        'form_id' => $this->visualForm->id,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
        'autocapitalize' => false,
    ]);
    $field->save();

    $options = $this->visualForms->makeOptions($field);
    $control = $this->visualForms->makeField($field);

    expect($control)
        ->toBeInstanceOf(TextInput::class)
        ->and($options)
        ->toBeNull();
});

it('makes options from database', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_key_attribute' => 'id',
        'options_value_attribute' => 'name',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 1],
        ],
        'options_order_by' => 'name',
        'options_order_direction' => 'asc',
    ]);
    $field->save();

    \DB::shouldReceive('table')
        ->with('categories')
        ->andReturnSelf();
    \DB::shouldReceive('where')
        ->with('active', '=', 1)
        ->andReturnSelf();
    \DB::shouldReceive('orderBy')
        ->with('name', 'asc')
        ->andReturnSelf();
    \DB::shouldReceive('get')
        ->andReturn(collect([
            (object) ['id' => 1, 'name' => 'Category 1'],
            (object) ['id' => 2, 'name' => 'Category 2'],
        ]));

    $options = $this->visualForms->makeOptions($field);

    expect($options)
        ->toBeInstanceOf(Collection::class)
        ->and($options->toArray())
        ->toEqual([1 => 'Category 1', 2 => 'Category 2']);
});

it('returns empty collection when table is null', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => null,
        'required' => true,
        'disabled' => false,
        'readonly' => false,
        'default_value' => 'John Doe',
        'autofocus' => false,
    ]);
    $field->save();

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class)
        ->and($options->isEmpty())
        ->toBeTrue();
});

it('adds where condition when or is not set', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 1],
        ],
    ]);
    $field->save();

    \DB::shouldReceive('table')->with('categories')->andReturnSelf();
    \DB::shouldReceive('where')->with('active', '=', 1)->andReturnSelf();
    \DB::shouldReceive('get')->andReturn(collect());

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class);
});

it('adds orWhere condition when or is set', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 1],
            ['column' => 'featured', 'operator' => '=', 'value' => 1, 'or' => true],
        ],
    ]);
    $field->save();

    \DB::shouldReceive('table')->with('categories')->andReturnSelf();
    \DB::shouldReceive('where')->with('active', '=', 1)->andReturnSelf();
    \DB::shouldReceive('orWhere')->with('featured', '=', 1)->andReturnSelf();
    \DB::shouldReceive('get')->andReturn(collect());

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class);
});

it('adds where condition with valid column, operator, and value', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 1],
        ],
    ]);
    $field->save();

    \DB::shouldReceive('table')->with('categories')->andReturnSelf();
    \DB::shouldReceive('where')->with('active', '=', 1)->andReturnSelf();
    \DB::shouldReceive('get')->andReturn(collect());

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class);
});

it('adds multiple where conditions', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 1],
            ['column' => 'featured', 'operator' => '=', 'value' => 1],
        ],
    ]);
    $field->save();

    \DB::shouldReceive('table')->with('categories')->andReturnSelf();
    \DB::shouldReceive('where')->with('active', '=', 1)->andReturnSelf();
    \DB::shouldReceive('where')->with('featured', '=', 1)->andReturnSelf();
    \DB::shouldReceive('get')->andReturn(collect());

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class);
});

it('returns empty collection when no conditions match', function () {
    $field = new VisualFormField([
        'label' => 'Category',
        'name' => 'category',
        'control_type' => ControlTypes::Select->value,
        'form_id' => $this->visualForm->id,
        'options_from_db' => true,
        'options_db_table' => 'categories',
        'options_where_conditions' => [
            ['column' => 'active', 'operator' => '=', 'value' => 0],
        ],
    ]);
    $field->save();

    \DB::shouldReceive('table')->with('categories')->andReturnSelf();
    \DB::shouldReceive('where')->with('active', '=', 0)->andReturnSelf();
    \DB::shouldReceive('get')->andReturn(collect());

    $options = $this->visualForms->makeOptions($field);

    expect($options)->toBeInstanceOf(Collection::class)
        ->and($options->isEmpty())->toBeTrue();
});
