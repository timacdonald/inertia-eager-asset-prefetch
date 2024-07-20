<?php

namespace TiMacDonald\Inertia;

use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class EagerPrefetchServiceProvider extends BaseServiceProvider
{
    public array $singletons = [
        Vite::class => EagerPrefetch::class,
    ];
}
