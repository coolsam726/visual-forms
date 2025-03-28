<?php

// config for Coolsam/VisualForms
return [
    'tables' => [
        'visual_forms' => env('VISUAL_FORMS_TABLE', 'visual_forms'),
        'visual_form_components' => env('VISUAL_FORM_COMPONENTS_TABLE', 'visual_form_components'),
        'visual_form_entries' => env('VISUAL_FORM_ENTRIES_TABLE', 'visual_form_entries'),
    ],
    'models' => [
        'visual_form' => \Coolsam\VisualForms\Models\VisualForm::class,
        'visual_form_component' => \Coolsam\VisualForms\Models\VisualFormComponent::class,
        'visual_form_entry' => \Coolsam\VisualForms\Models\VisualFormEntry::class,
    ],
    'resources' => [
        'visual-form' => \Coolsam\VisualForms\Filament\Resources\VisualFormResource::class,
        'visual-form-component' => \Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource::class,
    ],
    'policies' => [
        'visual-forms' => [
            'viewAny' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
            'view' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualForm $model) {
                return true;
            },
            'create' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return false;
            },
            'update' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualForm $model) {
                return true;
            },
            'delete' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualForm $model) {
                return true;
            },
            'deleteAny' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
            'restore' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualForm $model) {
                return true;
            },
            'forceDelete' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualForm $model) {
                return true;
            },
            'reorder' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
        ],
        'visual-form-components' => [
            'viewAny' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
            'view' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualFormComponent $model) {
                return true;
            },
            'create' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
            'update' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualFormComponent $model) {
                return true;
            },
            'delete' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualFormComponent $model) {
                return true;
            },
            'deleteAny' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
            'restore' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualFormComponent $model) {
                return true;
            },
            'forceDelete' => function (\Illuminate\Contracts\Auth\Authenticatable $user, \Coolsam\VisualForms\Models\VisualFormComponent $model) {
                return true;
            },
            'reorder' => function (\Illuminate\Contracts\Auth\Authenticatable $user) {
                return true;
            },
        ],
    ],
    'helpers-class' => \Coolsam\VisualForms\Support\FormHelpers::class, // feel free to extend this class and add your own methods
    'helpers' => [
        'form-settings-schema' => 'formSettingsSchema', // important to have this method in your helpers class
    ],
];
