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
        'visual-form' => [
            'resource' => \Coolsam\VisualForms\Filament\Resources\VisualFormResource::class,
            'model-label' => 'Forms',
            'navigation-icon' => 'heroicon-o-window',
            'navigation-group' => 'Form Designer',
            'navigation-label' => null,
            'navigation-sort' => 0,
            'cluster' => null,
        ],
        'visual-form-component' => [
            'resource' => \Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource::class,
            'model-label' => 'Form Component',
            'navigation-icon' => 'heroicon-o-window',
            'navigation-group' => 'Form Component',
            'navigation-label' => null,
            'navigation-sort' => 1,
            'cluster' => null,
        ],
        'visual-form-entry' => [
            'resource' => \Coolsam\VisualForms\Filament\Resources\VisualFormEntryResource::class,
            'model-label' => 'Submission',
            'navigation-icon' => 'heroicon-o-numbered-list',
            'navigation-group' => 'Form Designer',
            'navigation-label' => null,
            'navigation-sort' => 2,
            'cluster' => null,
        ],
    ],
    'policies' => [
        'visual_form' => null, // A policy class or null
        'visual_form_component' => null, // a policy class or null
        'visual_form_entry' => null,
    ],
    'helpers-class' => \Coolsam\VisualForms\Support\FormHelpers::class,
    'closures' => [
        'form-settings-schema' => function () {
            return [
                \Filament\Forms\Components\TextInput::make('contact_email')
                    ->placeholder(__('e.g suf@example.net'))
                    ->required()
                    ->prefixIcon('heroicon-o-envelope')
                    ->inlinePrefix()
                    ->email(),
                \Filament\Forms\Components\TextInput::make('contact_phone')->prefixIcon('heroicon-o-phone')
                    ->inlinePrefix()->required(),
                \Filament\Forms\Components\TextInput::make('notification_email_subject')->nullable(),
                \Filament\Forms\Components\MarkdownEditor::make('notification_email_body')->nullable()->columnSpanFull(),
            ];
        },
    ],
];
