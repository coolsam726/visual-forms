<?php

// config for Coolsam/VisualForms
return [
    'tables' => [
        'visual_forms' => env('VISUAL_FORMS_TABLE', 'visual_forms'),
        'visual_form_fields' => env('VISUAL_FORM_FIELDS_TABLE', 'visual_form_fields'),
        'visual_form_entries' => env('VISUAL_FORM_ENTRIES_TABLE', 'visual_form_entries'),
    ],
    'models' => [
        'visual_form' => env('VISUAL_FORM_MODEL', \Coolsam\VisualForms\Models\VisualForm::class),
        'visual_form_field' => env('VISUAL_FORM_FIELD_MODEL', \Coolsam\VisualForms\Models\VisualFormField::class),
        'visual_form_entry' => env('VISUAL_FORM_ENTRY_MODEL', \Coolsam\VisualForms\Models\VisualFormEntry::class),
    ],
];
