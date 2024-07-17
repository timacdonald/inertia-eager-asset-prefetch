<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use TiMacDonald\InertiaActivePreloading\ActivePreloading;

class ActivePreloadingTest extends TestCase
{
    public function testItOutputsTags()
    {
        $html = e(app(ActivePreloading::class)->toHtml());

        $this->assertSame(<<<HTML

        HTML, $html);
    }
}
