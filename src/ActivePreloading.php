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
    @php
        $buildDir = 'build';
        $strategy = '';

        $chunks = json_decode(file_get_contents(public_path('build/manifest.json')), flags: JSON_OBJECT_AS_ARRAY);

        $assets = collect($chunks)
            ->map(fn ($chunk) => $buildDir.'/'.$chunk['file'])
            ->sort(fn ($file) => str_ends_with($file, '.js'))
            ->filter(fn ($file) => str_ends_with($file, '.js') || str_ends_with($file, '.css'))
            ->reject(fn ($file) => collect(Vite::preloadedAssets())->contains(fn ($value, $preloaded) => str_ends_with($preloaded, $file)))
            ->values()
            ->pipe(fn ($assets) => Js::from($assets));

        @endphp
        <script id="__laravel-vite-eager-preloading">
             window.addEventListener('load', () => window.setTimeout(() => {
                const linkTemplate = document.createElement('link')
                linkTemplate.rel = 'prefetch'

                const makeLink = (asset) => {
                    const link = linkTemplate.cloneNode()
                    link.href = asset

                    if (asset.endsWith('.js')) {
                        linkTemplate.as = 'script'
                    } else if (asset.endsWith('.css')) {
                        linkTemplate.as = 'style'
                    }

                    return link
                }

                @if ($strategy === 'waterfall')
                    const loadNext = (assets) => window.setTimeout(() => {
                        const link = makeLink(assets.shift())
                        const next = nextIndex + 1

                        if (assets.length) {
                            link.onload = () => window.setTimeout(() => loadNext(assets))
                        }

                        document.head.append(link)
                    }, 0)

                    window.setTimeout(() => loadNext({{ $assets }}))
                @else
                    const fragment = new DocumentFragment
                    {{ $assets }}.forEach((asset) => fragment.append(makeLink(asset)))
                    document.head.append(fragment)
                @endif
             }))
        </script>
        $html = '';

        $chunks = json_decode(file_get_contents(__DIR__.'/../tests/manifest.json'), flags: JSON_OBJECT_AS_ARRAY);

        // sort by: js, css, images, other.
        // options for what resources to preload.
        // options to only preload js or css or images.
        // should this be based on the entry points? Maybe we should decorate the Vite class?
        // filter out all files already preloaded? Probably don't really need to.
        // developer may specify attributes also. not yet implemented.
        // waterfall, all at once, delay?

        $assets = collect($chunks)
            // ->sort()
            ->map(fn ($chunk) => $chunk['file'])
            ->values()
            ->pipe(fn ($assets) => Js::from($assets));

        // todo support vite CSP thingo?
        return <<<HTML
            <script id="__laravel-vite-eager-preloading">
                 window.addEventListener('load', () => window.setTimeout(() => {
                    const assets = {$assets};

                    const linkTemplate = document.createElement('link')
                    linkTemplate.fetchPriority = 'low'
                    linkTemplaate.rel = 'modulepreload'
                    linkTemplate.crossOrigin = 'crossorigin'

                    let previous;
                    const preloadModule = (href, current) => window.setTimeout(() => {
                        previous?.remove()

                        const next = current + 1
                        const link = linkTemplate.cloneNode()
                        link.href = assets[current]
                        previous = link

                        if (assets.length > next) {
                            link.onload = () => window.setTimeout(() => preloadModule(assets[current + 1], current + 1))
                        } else {
                            link.onload = () => window.setTimeout(() => link.remove())
                        }

                        document.head.insertAdjacentElement('beforeend', link)
                    }, 0)

                    window.setTimeout(() => preloadModule(assets[0], 0))
                })

                window.setTimeout(() => document.getElementById('__laravel-vite-eager-preloading').remove())
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
