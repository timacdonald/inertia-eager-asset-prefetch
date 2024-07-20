<?php

namespace TiMacDonald\InertiaEagerAssetPrefetch;

use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Vite::class, fn () => new class extends Vite {
            /**
             * The prefetching strategy to use.
             *
             * @var 'waterfall'|'aggressive'
             */
            protected $prefetchStrategy = 'waterfall';

            /**
             * When using the 'waterfall' strategy, the count of assets to load at one time.
             *
             * @param int
             */
            protected $prefetchChunks = 3;

            /**
             * Filter the prefetch assets.
             *
             * @param  callable|null
             */
            protected $prefetchFilter = null;

            /**
             * Set the prefetching strategy.
             *
             * @param  'waterfall'|'aggressive'  $strategy
             * @param  ...mixed  $extra
             */
            public function usePrefetchStrategy(string $strategy, mixed ...$extra): static
            {
                $this->prefetchStrategy = $strategy;

                if ($strategy === 'waterfall') {
                    $this->prefetchChunks = $extra[0] ?? 3;
                }

                return $this;
            }

            /**
             * Filter the assets to prefetch use the given callback.
             */
            public function usePrefetchFilter(callable $callback): static
            {
                $this->prefetchFilter = $callback;

                return $this;
            }

            /**
             * Generate Vite tags for an entrypoint.
             *
             * @param  string|string[]  $entrypoints
             * @param  string|null  $buildDirectory
             * @return \Illuminate\Support\HtmlString
             */
            public function __invoke($entrypoints, $buildDirectory = null)
            {
                if ($this->isRunningHot()) {
                    return parent::__invoke($entrypoints, $buildDirectory);
                }

                $html = parent::__invoke($entrypoints, $buildDirectory);

                $manifest = $this->manifest($buildDirectory ??= $this->buildDirectory);

                $assets = collect($manifest)
                    ->filter(fn ($chunk) => isset($chunk['file']))
                    ->filter(fn ($chunk) => str_ends_with($chunk['file'], '.js') || str_ends_with($chunk['file'], '.css'))
                    ->sort(fn ($chunk) => str_ends_with($chunk['file'], '.js'))
                    ->map(fn ($chunk) => collect([
                        ...$this->resolvePreloadTagAttributes(
                            $chunk['src'] ?? null,
                            $url = $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
                            $chunk,
                            $manifest,
                        ),
                        'rel' => 'prefetch',
                        'href' => $url,
                    ])->reject(fn ($value) => in_array($value, [null, false], true))->mapWithKeys(fn ($value, $key) => [
                        is_int($key) ? $value : $key => $value,
                    ]))
                        ->reject(fn ($attributes) => isset($this->preloadedAssets[$attributes['href']]))
                        ->values()
                        ->pipe(fn ($attributes) => Js::from($attributes)->toHtml());

                return match ($this->prefetchStrategy) {
                    'waterfall' => new HtmlString($html.<<<HTML
                        <script>
                             window.addEventListener('load', () => window.setTimeout(() => {
                                const linkTemplate = document.createElement('link')
                                linkTemplate.rel = 'prefetch'

                                const makeLink = (asset) => {
                                    const link = linkTemplate.cloneNode()

                                    Object.keys(asset).forEach((attribute) => {
                                        link.setAttribute(attribute, asset[attribute])
                                    })

                                    return link
                                }

                                const loadNext = (assets, count) => window.setTimeout(() => {
                                    const fragment = new DocumentFragment

                                    while (count > 0) {
                                        const link = makeLink(assets.shift())
                                        fragment.append(link)
                                        count--

                                        if (assets.length) {
                                            link.onload = () => loadNext(assets, 1)
                                            link.error = () => loadNext(assets, 1)
                                        }
                                    }

                                    document.head.append(fragment)
                                })

                                loadNext({$assets}, {$this->prefetchChunks})
                            }))
                        </script>
                        HTML),
                    'aggressive' => new HtmlString($html.<<<HTML
                        <script>
                             window.addEventListener('load', () => window.setTimeout(() => {
                                const linkTemplate = document.createElement('link')
                                linkTemplate.rel = 'prefetch'

                                const makeLink = (asset) => {
                                    const link = linkTemplate.cloneNode()

                                    Object.keys(asset).forEach((attribute) => {
                                        link.setAttribute(attribute, asset[attribute])
                                    })

                                    return link
                                }

                                const fragment = new DocumentFragment
                                {$assets}.forEach((asset) => fragment.append(makeLink(asset)))
                                document.head.append(fragment)
                             }))
                        </script>
                        HTML),
                };
            }
        });
    }
}
