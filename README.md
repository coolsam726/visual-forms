# Dynamically and Visually create forms in Filament and collect responses

[![Latest Version on Packagist](https://img.shields.io/packagist/v/coolsam/visual-forms.svg?style=flat-square)](https://packagist.org/packages/coolsam/visual-forms)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/coolsam/visual-forms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/coolsam726/visual-forms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/coolsam/visual-forms/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/coolsam726/visual-forms/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/coolsam/visual-forms.svg?style=flat-square)](https://packagist.org/packages/coolsam/visual-forms)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require coolsam/visual-forms
php artisan visual-forms:install
```
Follow the installation instructions to:
1. publish the config file
2. publish the migrations
3. run the migrations (Optional)

You can run the above steps manually by running the following commands:

To publish the migrations and run them
```bash
php artisan vendor:publish --tag="visual-forms-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="visual-forms-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

1. Register the VisualFormsPlugin in your AdminServiceProvider
```php
use Coolsam\VisualForms\Filament\VisualFormsPlugin;

// in your register method
$panel->plugin(VisualFormsPlugin::class);
```
The above command will register the VisualForms resource for managing forms from your backend.

2. In any of your forms, use the created form's schema to render it
```php
use Coolsam\VisualForms\Models\VisualForm;

// in your form's schema
public function form(Form $form)
{
   $recordId = 1;
    $formModel = VisualForm::find($recordId);
    return $form->schema($formModel->schema());
}
```
3. To save the form's response payload, execute the following method in your action method:
```php
use Coolsam\VisualForms\Models\VisualForm;

// in your form's action method e.g create()
public function create(Request $request, VisualForm $record)
{
    $data = $this->form->getState();
    $record->recordSubmission($data, isProcessed: false);
    // TODO: you can send this payload wherever else you want, even to a webhook for further processing   
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sam Maosa](https://github.com/coolsam726)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
