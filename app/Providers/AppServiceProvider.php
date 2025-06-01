<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Support\Assets\Js;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::statement("SET time_zone = '+08:00'");
        DatabaseNotifications::pollingInterval('10s');
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css')->loadedOnRequest(),
            Js::make('custom-filament-scripts', __DIR__ . '/../../resources/js/custom-filament-scripts.js'),
        ]);
        Filament::registerRenderHook(
            'head.start',
            fn () => '<meta name="user-id" content="' . auth()->id() . '">'
        );
    }
}
