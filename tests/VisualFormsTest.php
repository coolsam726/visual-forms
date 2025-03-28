<?php

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\VisualForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

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
