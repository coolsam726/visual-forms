<?php

namespace Coolsam\VisualForms;

use Coolsam\VisualForms\Filament\Resources\VisualFormEntryResource;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class VisualFormsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'visual-forms';
    }

    public function register(Panel $panel): void
    {
        $panel
//            ->discoverResources(in: __DIR__ . '/Filament/Resources', for: 'Coolsam\\VisualForms\\Filament\\Resources')
//            ->discoverPages(in: __DIR__ . '/Filament/Pages', for: 'Coolsam\\VisualForms\\Filament\\Pages')
        ->resources([
            \Config::get('visual-forms.resources.visual-form.resource'),
            \Config::get('visual-forms.resources.visual-form-entry.resource'),
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {

        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
