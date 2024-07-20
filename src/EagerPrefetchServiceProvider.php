<?php

namespace TiMacDonald\Inertia;

use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class EagerPrefetchServiceProvider extends ServiceProvider
{
    public array $singletons = [
        Vite::class => EagerPrefetch::class,
    ];
}
