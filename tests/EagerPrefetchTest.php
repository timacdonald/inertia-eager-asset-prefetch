<?php

namespace Tests;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Js;
use Orchestra\Testbench\TestCase;
use TiMacDonald\InertiaEagerAssetPrefetch\ServiceProvider;

class EagerPrefetchTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testItCanPrefetchEntrypoint()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
        ]);
        $this->assertSame(<<<HTML
        <link rel="preload" as="style" href="http://localhost/build/assets/index-B3s1tYeC.css" /><link rel="modulepreload" href="http://localhost/build/assets/app-lliD09ip.js" /><link rel="modulepreload" href="http://localhost/build/assets/index-BSdK3M0e.js" /><link rel="stylesheet" href="http://localhost/build/assets/index-B3s1tYeC.css" /><script type="module" src="http://localhost/build/assets/app-lliD09ip.js"></script>
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

                loadNext({$expectedAssets}, 3)
            }))
        </script>
        HTML, $html);
    }

    public function testItUsesDefaultChunksForWaterfall()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->usePrefetchStrategy('waterfall')->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
        ]);
        $this->assertStringContainsString(<<<JAVASCRIPT
                loadNext({$expectedAssets}, 3)
            JAVASCRIPT, $html);
    }

    public function testItHandlesSpecifyingPageWithAppJs()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/app.js', 'resources/js/Pages/Auth/Login.vue'])->usePrefetchStrategy('waterfall')->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
        ]);
        $this->assertStringContainsString(<<<JAVASCRIPT
                loadNext({$expectedAssets}, 3)
            JAVASCRIPT, $html);
    }

    public function testItCanSpecifyWaterfallChunks()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->usePrefetchStrategy('waterfall', 10)->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
        ]);
        $this->assertStringContainsString(<<<JAVASCRIPT
                loadNext({$expectedAssets}, 10)
            JAVASCRIPT, $html);
    }

    public function testItCanPrefetchAggressively()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->usePrefetchStrategy('aggressive')->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
        ]);

        $this->assertSame(<<<HTML
        <link rel="preload" as="style" href="http://localhost/build/assets/index-B3s1tYeC.css" /><link rel="modulepreload" href="http://localhost/build/assets/app-lliD09ip.js" /><link rel="modulepreload" href="http://localhost/build/assets/index-BSdK3M0e.js" /><link rel="stylesheet" href="http://localhost/build/assets/index-B3s1tYeC.css" /><script type="module" src="http://localhost/build/assets/app-lliD09ip.js"></script>
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
                {$expectedAssets}.forEach((asset) => fragment.append(makeLink(asset)))
                document.head.append(fragment)
             }))
        </script>
        HTML, $html);
    }

    public function testAddsAttributesToPrefetchTags()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) tap(Vite::withEntryPoints(['resources/js/app.js']))->useCspNonce('abc123')->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js", "nonce" => "abc123"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js", "nonce" => "abc123"],
        ]);
        $this->assertStringContainsString(<<<JAVASCRIPT
                loadNext({$expectedAssets}, 3)
        JAVASCRIPT, $html);
    }

    public function testItNormalisesAttributes()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) tap(Vite::withEntryPoints(['resources/js/app.js']))->usePreloadTagAttributes([
            'key' => 'value',
            'key-only',
            'true-value' => true,
            'false-value' => false,
            'null-value' => null,
        ])->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js", "key" => "value", "key-only" => "key-only", "true-value" => "true-value"],
        ]);

        $this->assertStringContainsString(<<<JAVASCRIPT
                loadNext({$expectedAssets}, 3)
        JAVASCRIPT, $html);
    }

    public function testItPrefetchesCss()
    {
        app()->usePublicPath(__DIR__);

        $html = (string) Vite::withEntryPoints(['resources/js/admin.js'])->toHtml();

        $expectedAssets = Js::from([
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ConfirmPassword-CDwcgU8E.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/GuestLayout-BY3LC-73.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/TextInput-C8CCB_U_.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/PrimaryButton-DuXwr-9M.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ApplicationLogo-BhIZH06z.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/_plugin-vue_export-helper-DlAUqK2U.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ForgotPassword-B0WWE0BO.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Login-DAFSdGSW.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Register-CfYQbTlA.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/ResetPassword-BNl7a4X1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/VerifyEmail-CyukB_SZ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Dashboard-DM_LxQy2.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/AuthenticatedLayout-DfWF52N1.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Edit-CYV2sXpe.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/DeleteUserForm-B1oHFaVP.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdatePasswordForm-CaeWqGla.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/UpdateProfileInformationForm-CJwkYwQQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/Welcome-D_7l79PQ.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/admin-runtime-import-CRvLQy6v.js"],
            ["rel" => "prefetch", "href" => "http://localhost/build/assets/admin-runtime-import-import-DKMIaPXC.js"],
            ["rel" => "prefetch", "as" => "style", "href" => "http://localhost/build/assets/admin-runtime-import-BlmN0T4U.css"],
        ]);
        $this->assertSame(<<<HTML
        <link rel="preload" as="style" href="http://localhost/build/assets/index-B3s1tYeC.css" /><link rel="preload" as="style" href="http://localhost/build/assets/admin-BctAalm_.css" /><link rel="modulepreload" href="http://localhost/build/assets/admin-Sefg0Q45.js" /><link rel="modulepreload" href="http://localhost/build/assets/index-BSdK3M0e.js" /><link rel="stylesheet" href="http://localhost/build/assets/index-B3s1tYeC.css" /><link rel="stylesheet" href="http://localhost/build/assets/admin-BctAalm_.css" /><script type="module" src="http://localhost/build/assets/admin-Sefg0Q45.js"></script>
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

                loadNext({$expectedAssets}, 3)
            }))
        </script>
        HTML, $html);
    }
}
