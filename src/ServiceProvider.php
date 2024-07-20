<?php

namespace TiMacDonald\InertiaEagerAssetPrefetch;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public array $singletons = [
        \Illuminate\Foundation\Vite::class => Vite::class,
    ];

    public function register(): void
    {
        dd('here');
    }

    public function boot(): void
    {
        dd('here');
    }
}
