<?php

namespace TiMacDonald\InertiaActivePreloading;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Js;
use Stringable;

class ActivePreloading implements Htmlable
{
    public function toHtml()
    {
        $html = '';

        $chunks = json_decode(file_get_contents(__DIR__.'/../tests/manifest.json'), flags: JSON_OBJECT_AS_ARRAY);

        // sort by: js, css, images, other.
        // options for what resources to preload.
        // options to only preload js or css or images.
        // should this be based on the entry points? Maybe we should decorate the Vite class?

        $assets = collect($chunks)
            // ->sort()
            ->filter(fn ($chunk) => ! $this->isCssPath($chunk['file']))
            ->map(fn ($chunk) => $chunk['file'])
            ->values()
            ->pipe(fn ($assets) => Js::from($assets));

        // temporary variable sadness...

        $files = collect($files)
            ->filter(fn ($files) => file_exists($files))
            ->map(fn ($file) => basename($file));

        $js = Js::from($files);

        // `pipe` goodness...

        $js = collect($files)
            ->filter(fn ($files) => file_exists($files))
            ->map(fn ($file) => basename($file))
            ->pipe(fn ($file) => Js::from($file));

        // todo support vite CSP thingo?
        return <<<HTML
        <script id="__laravel-vite-eager-preloading">
         window.addEventListener('load', () => window.setTimeout(() => {
            // $assets contains all the site assets not yet preloaded...
            const assets = @js($assets)

            const link = document.createElement('link')
            link.fetchPriority = 'low'
            link.rel = 'modulepreload'
            link.crossOrigin = 'crossorigin'
            // developer may specify attributes also. not yet implemented.

            let previous;
            const preloadModule = (href, current) => window.setTimeout(() => {
                previous?.remove()

                const next = current + 1
                const el = link.cloneNode()
                el.href = assets[current]
                previous = el

                if (assets.length > next) {
                    el.onload = () => preloadModule(assets[current + 1], current + 1)
                } else {
                    el.onload = () => el.remove()
                }

                document.head.insertAdjacentElement('beforeend', el)
            }, 0)

            preloadModule(assets[0], 0)
        }, 3000))

        document.getElementById('__laravel-vite-eager-preloading').remove()
        </script>
        HTML;
    }

    /**
     * Determine whether the given path is a CSS file.
     *
     * @param  string  $path
     * @return bool
     */
    protected function isCssPath($path)
    {
        return preg_match('/\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/', $path) === 1;
    }
}
