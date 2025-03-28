<?php

use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\VisualForms;

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
