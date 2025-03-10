<?php

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormField;
use Coolsam\VisualForms\VisualForms;
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
