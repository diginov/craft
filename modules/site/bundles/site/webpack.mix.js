const mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath('./dist');

mix.options({
    manifest: false,
    processCssUrls: false,
    cssNano: { discardComments: { removeAll: true } },
});

mix.webpackConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, './src'),
            "@craft": path.resolve(__dirname, '../../../../vendor/craftcms/cms/src/web/assets/cp/dist'),
            "@garnish": path.resolve(__dirname, '../../../../vendor/craftcms/cms/src/web/assets/garnish/dist'),
        }
    },
    externals: {
        '@craft/cp': "Craft",
        '@garnish/garnish': "Garnish",
        jquery: "$",
        vue: "Vue",
    },
    stats: {
        children: false,
    },
});

mix.js('./src/js/main.js', 'bundle.js').vue({ version: 2 });

mix.css('./src/css/main.pcss', 'bundle.css', [
    require('postcss-import'),
    require('tailwindcss/nesting'),
    require('tailwindcss'),
]);
