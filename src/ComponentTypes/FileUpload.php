<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms;

class FileUpload extends Field
{
    public function getOptionName(): string
    {
        return __('File Upload');
    }

    public function letThereBe(string $name): Forms\Components\Component
    {
        return Forms\Components\FileUpload::make($name);
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            ...parent::getSpecificBasicSchema(),
            Forms\Components\Fieldset::make(__('File Upload Options'))
                ->schema([
                    Forms\Components\TextInput::make('disk')->label(__('Disk'))
                        ->placeholder('local')
                        ->helperText(__('The disk to use for file uploads.')),
                    Forms\Components\TextInput::make('directory')->label(__('Directory'))
                        ->placeholder('uploads')
                        ->helperText(__('The directory to use for file uploads.')),
                    Forms\Components\ToggleButtons::make('visibility')->label(__('Visibility'))
                        ->inline()->options([
                            'public' => 'Public',
                            'private' => 'Private',
                        ])
                        ->helperText(__('The visibility to use for file uploads.')),
                    Forms\Components\TextInput::make('maxParallelUploads')->label(__('Max Parallel Uploads'))
                        ->placeholder('e.g 1')
                        ->numeric()->integer()
                        ->visible(fn ($get) => Utils::getBool($get('multiple')))
                        ->helperText(__('The maximum number of files that can be uploaded at once.')),
                    Forms\Components\ToggleButtons::make('preserveFilenames')->label(__('Preserve Filenames'))
                        ->inline()
                        ->boolean()
                        ->default(false)
                        ->helperText(__('Whether to preserve the original filenames of the uploaded files.')),
                    Forms\Components\TextInput::make('storeFileNamesIn')->label(__('Store File Names in'))
                        ->placeholder('e.g attachment_file_names')
                        ->helperText(__('The name of the field to store the file names in.')),
                    Forms\Components\ToggleButtons::make('image')->label(__('Image'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to use the file upload as an image.')),
                    Forms\Components\ToggleButtons::make('avatar')->label(__('Avatar'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to use the file upload as an avatar.')),
                    Forms\Components\ToggleButtons::make('reorderable')->label(__('Re-orderable'))
                        ->inline()
                        ->boolean()
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('multiple')))
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to allow reordering of the uploaded files.')),
                    Forms\Components\ToggleButtons::make('openable')
                        ->label(__('Openable'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to allow opening of the uploaded files.')),
                    Forms\Components\ToggleButtons::make('downloadable')
                        ->label(__('Downloadable'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to allow downloading of the uploaded files.')),

                    Forms\Components\ToggleButtons::make('previewable')
                        ->label(__('Previewable'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(true)
                        ->helperText(__('Whether to allow previewing of the uploaded files.')),
                    Forms\Components\ToggleButtons::make('moveFiles')
                        ->label(__('Move Files'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Moving files instead of copying when the form is submitted')),
                    Forms\Components\ToggleButtons::make('storeFiles')
                        ->label(__('Store Files'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(true)
                        ->helperText(__('Whether to store the uploaded files in the database.')),
                    Forms\Components\ToggleButtons::make('orientImagesFromExif')
                        ->label(__('Orient Images From Exif'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(true)
                        ->helperText(__('Whether to orient images from EXIF data.')),
                    Forms\Components\ToggleButtons::make('deletable')->label(__('Deletable'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(true)
                        ->helperText(__('Whether to allow deleting of the uploaded files.')),

                    Forms\Components\ToggleButtons::make('fetchFileInformation')
                        ->label(__('Fetch File Information'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(true)
                        ->helperText(__('Whether to fetch file information from the server.')),

                    Forms\Components\TextInput::make('uploadingMessage')->label(__('Uploading Message'))
                        ->placeholder('e.g Uploading...')
                        ->helperText(__('The message to display while uploading files.')),
                ]),
            Forms\Components\Fieldset::make(__('Image Upload Options'))
                ->visible(fn (Forms\Get $get) => Utils::getBool($get('image')) || Utils::getBool($get('avatar')))
                ->schema([
                    Forms\Components\ToggleButtons::make('imageEditor')->label(__('Image Editor'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to use the image editor.')),
                    Forms\Components\ToggleButtons::make('imageEditorMode')->label(__('Image Editor Mode'))
                        ->inline()
                        ->options([
                            1 => 'One',
                            2 => 'Two',
                            3 => 'Three',
                        ])->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->live()
                        ->default(false)
                        ->helperText(__('Whether to use the image editor mode.')),
                    Forms\Components\TagsInput::make('imageEditorAspectRatios')->label(__('Image Editor Aspect Ratios'))
                        ->placeholder('e.g 16:9, 4:3')
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->helperText(__('The aspect ratios to use for the image editor.')),
                    Forms\Components\ColorPicker::make('imageEditorEmptyFillColor')
                        ->label(__('Image Editor Empty Fill Color'))
                        ->placeholder('#ffffff')
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->helperText(__('The empty fill color to use for the image editor.')),
                    Forms\Components\TextInput::make('imageEditorViewportWidth')->label(__('Image Editor Viewport Width'))
                        ->placeholder('1920')
                        ->numeric()->integer()
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->helperText(__('The viewport width to use for the image editor.')),
                    Forms\Components\TextInput::make('imageEditorViewportHeight')->label(__('Image Editor Viewport Height'))
                        ->placeholder('1080')
                        ->numeric()->integer()
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->helperText(__('The viewport height to use for the image editor.')),
                    Forms\Components\ToggleButtons::make('circleCropper')->label(__('Circle Cropper'))
                        ->inline()
                        ->boolean()
                        ->live()
                        ->visible(fn (Forms\Get $get) => Utils::getBool($get('imageEditor')))
                        ->default(false)
                        ->helperText(__('Whether to use the circle cropper.')),

                    // Without editor
                    Forms\Components\ToggleButtons::make('imageResizeMode')->label(__('Image Resize Mode'))
                        ->inline()
                        ->options([
                            'force' => 'Force',
                            'cover' => 'Cover',
                            'contain' => 'Contain',
                            null => 'None',
                        ])
                        ->visible(fn (Forms\Get $get) => ! Utils::getBool($get('imageEditor')))
                        ->live()
                        ->default(false)
                        ->helperText(__('The resize mode to use for the image.')),
                    Forms\Components\TextInput::make('imageCropAspectRatio')
                        ->label(__('Image Crop Aspect Ratio'))
                        ->placeholder('e.g 16:9')
                        ->visible(fn (Forms\Get $get) => ! Utils::getBool($get('imageEditor')))
                        ->helperText(__('The aspect ratio to use for the image crop.')),
                    Forms\Components\TextInput::make('imageResizeTargetWidth')
                        ->label(__('Image Resize Target Width'))
                        ->placeholder('1920')
                        ->numeric()->integer()
                        ->visible(fn (Forms\Get $get) => ! Utils::getBool($get('imageEditor')))
                        ->helperText(__('The target width to use for the image resize.')),
                    Forms\Components\TextInput::make('imageResizeTargetHeight')
                        ->label(__('Image Resize Target Height'))
                        ->placeholder('1080')
                        ->numeric()->integer()
                        ->visible(fn (Forms\Get $get) => ! Utils::getBool($get('imageEditor')))
                        ->helperText(__('The target height to use for the image resize.')),
                ]),
            Forms\Components\Fieldset::make(__('File Upload Appearance Options'))
                ->schema([
                    Forms\Components\TextInput::make('imagePreviewHeight')->label(__('Image Preview Height'))
                        ->numeric()
                        ->integer()
                        ->placeholder('250')
                        ->helperText(__('The height of the image preview.')),

                    Forms\Components\ToggleButtons::make('panelLayout')
                        ->label(__('Panel Layout'))
                        ->inline()
                        ->options([
                            'integrated' => 'Integrated',
                            'compact' => 'Compact',
                            'circle' => 'Circle',
                            'grid' => 'Grid',
                        ])
                        ->helperText(__('The layout of the panel.')),

                    Forms\Components\TextInput::make('panelAspectRatio')
                        ->label(__('Panel Aspect Ratio'))
                        ->placeholder('e.g 2:1')
                        ->helperText(__('The aspect ratio of the panel.')),
                    Forms\Components\ToggleButtons::make('loadingIndicatorPosition')
                        ->label(__('Loading Indicator Position'))
                        ->inline()
                        ->options([
                            'left' => 'Left',
                            'center' => 'center',
                            'right' => 'Right',
                        ])
                        ->helperText(__('The position of the loading indicator.')),
                    Forms\Components\ToggleButtons::make('removeUploadedFileButtonPosition')
                        ->label(__('Remove Uploaded File Button Position'))
                        ->inline()
                        ->options([
                            'left' => 'Left',
                            'center' => 'center',
                            'right' => 'Right',
                        ])
                        ->helperText(__('The position of the remove uploaded file button.')),

                    Forms\Components\ToggleButtons::make('uploadButtonPosition')
                        ->label(__('Upload Button Position'))
                        ->inline()
                        ->options([
                            'left' => 'Left',
                            'center' => 'center',
                            'right' => 'Right',
                        ])
                        ->helperText(__('The position of the upload button.')),
                    Forms\Components\ToggleButtons::make('uploadProgressIndicatorPosition')
                        ->label(__('Upload Progress Indicator Position'))
                        ->inline()
                        ->options([
                            'left' => 'Left',
                            'center' => 'center',
                            'right' => 'Right',
                        ])
                        ->helperText(__('The position of the upload progress indicator.')),

                ]),

        ];
    }

    public function getSpecificValidationSchema(): array
    {
        return [
            Forms\Components\Fieldset::make()->schema([
                Forms\Components\TextInput::make('minSize')->label(__('Min Size'))
                    ->placeholder('e.g 0')
                    ->suffix('Kilobytes')
                    ->numeric()->integer()
                    ->helperText(__('The minimum size of the uploaded files in KB.')),
                Forms\Components\TextInput::make('maxSize')->label(__('Max Size'))
                    ->placeholder('e.g 1024')
                    ->suffix('Kilobytes')
                    ->numeric()->integer()
                    ->helperText(__('The maximum size of the uploaded files in KB.')),

                Forms\Components\TextInput::make('minFiles')->label(__('Min no. of Files'))
                    ->placeholder('e.g 1')
                    ->numeric()->integer()
                    ->visible(fn (Forms\Get $get) => Utils::getBool($get('multiple')))
                    ->minValue(0)
                    ->helperText(__('The minimum number of files that can be uploaded.')),

                Forms\Components\TextInput::make('maxFiles')->label(__('Max no. of Files'))
                    ->placeholder('e.g 5')
                    ->numeric()->integer()
                    ->minValue(0)
                    ->visible(fn (Forms\Get $get) => Utils::getBool($get('multiple')))
                    ->helperText(__('The maximum number of files that can be uploaded.')),

                Forms\Components\TagsInput::make('acceptedFileTypes')->label(__('Accepted File Types'))
                    ->placeholder('e.g image/*, application/pdf')
                    ->hint(__('Use mime types e.g image/*, application/pdf, text/plain'))
                    ->helperText(__('The accepted file types for the uploaded files.'))
                    ->visible(fn (Forms\Get $get) => Utils::getBool($get('multiple')))
                    ->suggestions([
                        'application/*',
                        'application/json',
                        'application/javascript',
                        'application/pdf',
                        'application/xml',
                        'application/zip',
                        'application/x-www-form-urlencoded',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/octet-stream',
                        'audio/*',
                        'audio/mpeg',
                        'audio/ogg',
                        'audio/wav',
                        'audio/webm',
                        'image/*',
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                        'image/svg+xml',
                        'image/webp',
                        'text/*',
                        'text/css',
                        'text/html',
                        'text/plain',
                        'text/xml',
                        'video/*',
                        'video/mp4',
                        'video/mpeg',
                        'video/ogg',
                        'video/webm',
                        'video/x-msvideo',
                    ])
                    ->columnSpanFull(),

            ])->columns(['md' => 2, 'xl' => 3, '2xl' => 4]),
            ...parent::getSpecificValidationSchema(),
        ];
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);

        $props = $this->getProps();
        if (filled($props)) {
            if (filled($props->get('disk')) && method_exists($component, 'disk')) {
                $component->disk($props->get('disk'));
            }

            if (filled($props->get('directory')) && method_exists($component, 'directory')) {
                $component->directory($props->get('directory'));
            }

            if (filled($props->get('visibility')) && method_exists($component, 'visibility')) {
                $component->visibility($props->get('visibility'));
            }

            if (filled($props->get('maxFiles')) && method_exists($component, 'maxFiles')) {
                $component->maxFiles($props->get('maxFiles'));
            }

            if (filled($props->get('maxSize')) && method_exists($component, 'maxSize')) {
                $component->maxSize($props->get('maxSize'));
            }

            if (filled($props->get('multiple')) && method_exists($component, 'multiple')) {
                $multiple = Utils::getBool($props->get('multiple'));
                $component->multiple($multiple);
                if ($multiple && filled($props->get('maxParallelUploads')) && method_exists($component, 'maxParallelUploads')) {
                    $component->maxParallelUploads($props->get('maxParallelUploads'));
                }
            }

            if (filled($props->get('preserveFilenames')) && method_exists($component, 'preserveFilenames')) {
                $component->preserveFilenames(Utils::getBool($props->get('preserveFilenames')));
            }

            if (filled($props->get('storeFileNamesIn')) && method_exists($component, 'storeFileNamesIn')) {
                $component->storeFileNamesIn($props->get('storeFileNamesIn'));
            }

            if (filled($props->get('image')) && method_exists($component, 'image')) {
                $component->image(Utils::getBool($props->get('image')));
            }

            if (filled($props->get('avatar')) && method_exists($component, 'avatar')) {
                $component->avatar(Utils::getBool($props->get('avatar')));
            }

            if (filled($props->get('reorderable')) && method_exists($component, 'reorderable')) {
                $component->reorderable($reorderable = Utils::getBool($props->get('reorderable')));
                if ($reorderable && method_exists($component, 'appendFiles')) {
                    $component->appendFiles();
                }
            }

            if (filled($props->get('openable')) && method_exists($component, 'openable')) {
                $component->openable(Utils::getBool($props->get('openable')));
            }

            if (filled($props->get('downloadable')) && method_exists($component, 'downloadable')) {
                $component->downloadable(Utils::getBool($props->get('downloadable')));
            }

            if (filled($props->get('previewable')) && method_exists($component, 'previewable')) {
                $component->previewable(Utils::getBool($props->get('previewable')));
            }

            if (filled($props->get('moveFiles')) && method_exists($component, 'moveFiles')) {
                $component->moveFiles(Utils::getBool($props->get('moveFiles')));
            }

            if (filled($props->get('storeFiles')) && method_exists($component, 'storeFiles')) {
                $component->storeFiles(Utils::getBool($props->get('storeFiles')));
            }

            if (filled($props->get('orientImagesFromExif')) && method_exists($component, 'orientImagesFromExif')) {
                $component->orientImagesFromExif(Utils::getBool($props->get('orientImagesFromExif')));
            }

            if (filled($props->get('deletable')) && method_exists($component, 'deletable')) {
                $component->deletable(Utils::getBool($props->get('deletable')));
            }

            if (filled($props->get('fetchFileInformation')) && method_exists($component, 'fetchFileInformation')) {
                $component->fetchFileInformation(Utils::getBool($props->get('fetchFileInformation')));
            }

            if (filled($props->get('uploadingMessage')) && method_exists($component, 'uploadingMessage')) {
                $component->uploadingMessage($props->get('uploadingMessage'));
            }

            if (filled($props->get('imagePreviewHeight')) && method_exists($component, 'imagePreviewHeight')) {
                $component->imagePreviewHeight($props->get('imagePreviewHeight'));
            }

            if (filled($props->get('panelLayout')) && method_exists($component, 'panelLayout')) {
                $component->panelLayout($props->get('panelLayout'));
            }

            if (filled($props->get('panelAspectRatio')) && method_exists($component, 'panelAspectRatio')) {
                $component->panelAspectRatio($props->get('panelAspectRatio'));
            }

            if (filled($props->get('loadingIndicatorPosition')) && method_exists($component, 'loadingIndicatorPosition')) {
                $component->loadingIndicatorPosition($props->get('loadingIndicatorPosition'));
            }

            if (filled($props->get('removeUploadedFileButtonPosition')) && method_exists($component, 'removeUploadedFileButtonPosition')) {
                $component->removeUploadedFileButtonPosition($props->get('removeUploadedFileButtonPosition'));
            }

            if (filled($props->get('uploadButtonPosition')) && method_exists($component, 'uploadButtonPosition')) {
                $component->uploadButtonPosition($props->get('uploadButtonPosition'));
            }

            if (filled($props->get('uploadProgressIndicatorPosition')) && method_exists($component, 'uploadProgressIndicatorPosition')) {
                $component->uploadProgressIndicatorPosition($props->get('uploadProgressIndicatorPosition'));
            }

            if (filled($props->get('imageEditor')) && method_exists($component, 'imageEditor')) {
                $component->imageEditor(Utils::getBool($props->get('imageEditor')));
            }

            $editor = Utils::getBool($props->get('imageEditor'));
            if ($editor) {
                if (filled($props->get('imageEditorMode')) && method_exists($component, 'imageEditorMode')) {
                    $component->imageEditorMode($props->get('imageEditorMode'));
                }

                if (filled($props->get('imageEditorAspectRatios')) && method_exists($component, 'imageEditorAspectRatios')) {
                    $component->imageEditorAspectRatios($props->get('imageEditorAspectRatios'));
                }

                if (filled($props->get('imageEditorEmptyFillColor')) && method_exists($component, 'imageEditorEmptyFillColor')) {
                    $component->imageEditorEmptyFillColor($props->get('imageEditorEmptyFillColor'));
                }

                if (filled($props->get('imageEditorViewportWidth')) && method_exists($component, 'imageEditorViewportWidth')) {
                    $component->imageEditorViewportWidth($props->get('imageEditorViewportWidth'));
                }

                if (filled($props->get('imageEditorViewportHeight')) && method_exists($component, 'imageEditorViewportHeight')) {
                    $component->imageEditorViewportHeight($props->get('imageEditorViewportHeight'));
                }

                if (filled($props->get('circleCropper')) && method_exists($component, 'circleCropper')) {
                    $component->circleCropper(Utils::getBool($props->get('circleCropper')));
                }
            }

            if (filled($props->get('imageResizeMode')) && method_exists($component, 'imageResizeMode')) {
                $component->imageResizeMode($props->get('imageResizeMode'));
            }

            if (filled($props->get('imageCropAspectRatio')) && method_exists($component, 'imageCropAspectRatio')) {
                $component->imageCropAspectRatio($props->get('imageCropAspectRatio'));
            }

            if (filled($props->get('imageResizeTargetWidth')) && method_exists($component, 'imageResizeTargetWidth')) {
                $component->imageResizeTargetWidth($props->get('imageResizeTargetWidth'));
            }

            if (filled($props->get('imageResizeTargetHeight')) && method_exists($component, 'imageResizeTargetHeight')) {
                $component->imageResizeTargetHeight($props->get('imageResizeTargetHeight'));
            }

            // Validation
            if (filled($props->get('minSize')) && method_exists($component, 'minSize')) {
                $component->minSize($props->get('minSize'));
            }

            if (filled($props->get('maxSize')) && method_exists($component, 'maxSize')) {
                $component->maxSize($props->get('maxSize'));
            }
            $multiple = Utils::getBool($props->get('multiple'));
            if ($multiple && filled($props->get('minFiles')) && method_exists($component, 'minFiles')) {
                $component->minFiles($props->get('minFiles'));
            }

            if ($multiple && filled($props->get('maxFiles')) && method_exists($component, 'maxFiles')) {
                $component->maxFiles($props->get('maxFiles'));
            }

            if (filled($props->get('acceptedFileTypes')) && method_exists($component, 'acceptedFileTypes')) {
                $component->acceptedFileTypes($props->get('acceptedFileTypes'));
            }
        }
    }
}
