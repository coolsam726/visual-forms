<?php

namespace Coolsam\VisualForms;

use Coolsam\VisualForms\Livewire\EditVisualComponent;
use Coolsam\VisualForms\Testing\TestsVisualForms;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VisualFormsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'visual-forms';

    public static string $viewNamespace = 'visual-forms';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('coolsam/visual-forms');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->register(NestedSetServiceProvider::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function packageBooted(): void
    {
        // Register policies
        $this->registerPolicies();
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        $this->callAfterResolving(BladeCompiler::class, function () {
            Livewire::component('edit-visual-component', EditVisualComponent::class);
        });

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/visual-forms/{$file->getFilename()}"),
                ], 'visual-forms-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsVisualForms);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'coolsam/visual-forms';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('visual-forms', __DIR__ . '/../resources/dist/components/visual-forms.js'),
            Css::make('visual-forms-styles', __DIR__ . '/../resources/dist/visual-forms.css'),
            Js::make('visual-forms-scripts', __DIR__ . '/../resources/dist/visual-forms.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_visual_forms_tables',
        ];
    }

    protected function registerPolicies(): void
    {
        $policies = config('visual-forms.policies');

        // register policies
        foreach ($policies as $model => $policy) {
            if (! $policy) {
                continue;
            }
            $modelClass = config("visual-forms.models.{$model}");
            if (! $modelClass) {
                continue;
            }
            \Gate::policy($modelClass, $policy);
        }
    }
}
