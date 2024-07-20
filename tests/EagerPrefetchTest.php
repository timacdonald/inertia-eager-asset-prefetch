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
                        link[attribute] = asset[attribute]
                    })

                    return link
                }

                const loadNext = (assets) => window.setTimeout(() => {
                    const link = makeLink(assets.shift())
                    const next = nextIndex + 1

                    if (assets.length) {
                        link.onload = () => loadNext(assets)
                        link.error = () => loadNext(assets)
                    }

                    document.head.append(link)
                }, 0)

                loadNext(JSON.parse('[{\u0022rel\u0022:\u0022prefetch\u0022,\u0022as\u0022:\u0022style\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-ChJ_j-JJ.css\u0022,\u0022nonce\u0022:false,\u0022crossorigin\u0022:false,\u0022integrity\u0022:false},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/foo-BRBmoGS9.js\u0022,\u0022nonce\u0022:false,\u0022crossorigin\u0022:false,\u0022integrity\u0022:false},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/bar-gkvgaI9m.js\u0022,\u0022nonce\u0022:false,\u0022crossorigin\u0022:false,\u0022integrity\u0022:false},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/baz-B2H3sXNv.js\u0022,\u0022nonce\u0022:false,\u0022crossorigin\u0022:false,\u0022integrity\u0022:false},{\u0022rel\u0022:\u0022prefetch\u0022,\u0022href\u0022:\u0022http:\\\/\\\/localhost\\\/build\\\/assets\\\/shared-B7PI925R.js\u0022,\u0022nonce\u0022:false,\u0022crossorigin\u0022:false,\u0022integrity\u0022:false}]'))
            }))
        </script>
        HTML, $html);
    }
}
