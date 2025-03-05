<?php

namespace Coolsam\VisualForms;

enum ControlTypes
{
    // Populate from Filament/Forms/Components which are not deprecated
    case TextInput;
    case Textarea;
    case Select;
    case Radio;
    case Checkbox;
    case FileUpload;
    case Datepicker;
    case Timepicker;
    case Datetimepicker;
    case RichEditor;
    case MarkdownEditor;
    case CodeEditor;
    case Password;
    case Hidden;
}