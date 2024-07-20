<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use TiMacDonald\Inertia\EagerPrefetch;

class EagerPrefetchTest extends TestCase
{
    public function testItOutputsTags()
    {
        app()->usePublicPath(__DIR__);
        $html = (string) app(EagerPrefetch::class)([]);

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
                    while (count > 0) {
                        const link = makeLink(assets.shift())

                        if (assets.length) {
                            link.onload = () => loadNext(assets, 1)
                            link.error = () => loadNext(assets, 1)
                        }

                        document.head.append(link)
                        count--
                    }
                })

                loadNext(JSON.parse('[{\u0022rel\u0022:\u0022prefetch\u0022,\u0022as\u0022:\u0022style\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-ChJ_j-JJ.css\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/foo-BRBmoGS9.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/bar-gkvgaI9m.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/baz-B2H3sXNv.js\u0022},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-B7PI925R.js\u0022}]'), 3)
            }))
        </script>
        HTML, $html);
    }
}
