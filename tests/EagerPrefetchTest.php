<?php

namespace Tests;

use Illuminate\Support\Facades\Vite;
use Orchestra\Testbench\TestCase;

class EagerPrefetchTest extends TestCase
{
    public function testItCanPrefetch()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::toHtml();

        $this->assertSame(<<<'HTML'
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

                loadNext(JSON.parse('[{\u0022rel\u0022:\u0022prefetch\u0022,\u0022as\u0022:\u0022style\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-ChJ_j-JJ.css\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/foo-BRBmoGS9.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/bar-gkvgaI9m.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/baz-B2H3sXNv.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-B7PI925R.js\u0022}]'), 3)
            }))
        </script>
        HTML, $html);
    }

    public function testItCanPrefetchAggressively()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::prefetchStrategy('aggressive')->toHtml();

        $this->assertSame(<<<'HTML'
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
                JSON.parse('[{\u0022rel\u0022:\u0022prefetch\u0022,\u0022as\u0022:\u0022style\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-ChJ_j-JJ.css\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/foo-BRBmoGS9.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/bar-gkvgaI9m.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/baz-B2H3sXNv.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-B7PI925R.js\u0022}]').forEach((asset) => fragment.append(makeLink(asset)))
                document.head.append(fragment)
             }))
        </script>
        HTML, $html);
    }
}
