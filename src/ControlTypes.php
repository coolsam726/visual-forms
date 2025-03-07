<?php

namespace Coolsam\VisualForms;

enum ControlTypes: string
{
    case TextInput = 'TextInput';
    case Textarea = 'Textarea';
    case Select = 'Select';
    case Radio = 'Radio';
    case Toggle = 'Toggle';
    case CheckboxList = 'CheckboxList';
    case Checkbox = 'Checkbox';
    case FileUpload = 'FileUpload';
    case DatePicker = 'DatePicker';
    case TimePicker = 'TimePicker';
    case DateTimePicker = 'DateTimePicker';
    case RichEditor = 'RichEditor';
    case MarkdownEditor = 'MarkdownEditor';
    case Repeater = 'Repeater';
    case TagsInput = 'TagsInput';
    case Hidden = 'Hidden';
    case KeyValue = 'KeyValue';
    case ColorPicker = 'ColorPicker';
    case ToggleButtons = 'ToggleButtons';

    case TableRepeater = 'TableRepeater';

    public static function hasAutocomplete(string $controlType): bool
    {
        return in_array($controlType, [
            self::TextInput->value,
            self::Textarea->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasAutoCapitalize(string $type): bool
    {
        return in_array($type, [
            self::TextInput->value,
            self::Textarea->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasAutofocus(string $type): bool
    {
        return in_array($type, [
            self::TextInput->value,
            self::Textarea->value,
            self::Select->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasReadonly(string $type): bool
    {
        return in_array($type, [
            self::TextInput->value,
            self::Textarea->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasOptions(string $type): bool
    {
        return in_array($type, [
            self::Select->value,
            self::Radio->value,
            self::CheckboxList->value,
            self::ToggleButtons->value,
        ]);
    }

    public static function hasPlaceholder(string $type): bool
    {
        return in_array($type, [
            self::TextInput->value,
            self::Textarea->value,
            self::Select->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasSearchable(string $type): bool
    {
        return in_array($type, [
            self::Select->value,
            self::TagsInput->value,
        ]);
    }

    public static function hasPrefixAndSuffix(string $type): bool
    {
        return in_array($type, [
            self::TextInput->value,
            self::Textarea->value,
            self::Select->value,
            self::TagsInput->value,
        ]);
    }
}
