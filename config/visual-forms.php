<?php

// config for Coolsam/VisualForms
return [
    'tables' => [
        'visual_forms' => env('VISUAL_FORMS_TABLE', 'visual_forms'),
        'visual_form_fields' => env('VISUAL_FORM_FIELDS_TABLE', 'visual_form_fields'),
        'visual_form_components' => env('VISUAL_FORM_COMPONENTS_TABLE', 'visual_form_components'),
        'visual_form_entries' => env('VISUAL_FORM_ENTRIES_TABLE', 'visual_form_entries'),
    ],
    'models' => [
        'visual_form' => \Coolsam\VisualForms\Models\VisualForm::class,
        'visual_form_field' => \Coolsam\VisualForms\Models\VisualFormField::class,
        'visual_form_component' => \Coolsam\VisualForms\Models\VisualFormComponent::class,
        'visual_form_entry' => \Coolsam\VisualForms\Models\VisualFormEntry::class,
    ],
    'resources' => [
        'visual-form' => \Coolsam\VisualForms\Filament\Resources\VisualFormResource::class,
        'visual-form-component' => \Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource::class,
    ],
];
