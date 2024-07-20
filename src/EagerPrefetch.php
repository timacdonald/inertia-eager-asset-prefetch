<?php

namespace TiMacDonald\Inertia;

use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Stringable;

class EagerPrefetch extends Vite
{
    /**
     * The prefetching strategy to use.
     *
     * @var 'waterfall'|'aggressive'
     */
    protected string $strategy = 'waterfall';

    /**
     * When using the 'waterfall' strategy, the count of assets to load at one time.
     */
    protected int $chunks = 3;

    /**
     * Set the prefetching strategy.
     *
     * @param  'waterfall'|'aggressive'  $strategy
     */
    public function prefetchStrategy(string $strategy): static
    {
        $this->strategy = $strategy;

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

        $html .= <<<HTML
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
            HTML;

            if ($this->strategy === 'waterfall') {
                $html .= <<<HTML


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

                        loadNext({$assets}, {$this->chunks})
                    }))
                </script>
                HTML;

                } else {

                $html .= <<<JS


                        const fragment = new DocumentFragment
                        {$assets}.forEach((asset) => fragment.append(makeLink(asset)))
                        document.head.append(fragment)
                     }))
                </script>
                JS;
                }

            return new HtmlString($html);
    }
}
