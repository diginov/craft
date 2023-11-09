const mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath('./web/assets');

mix.options({
    manifest: false,
    clearConsole: false,
    processCssUrls: false,
    terser: {
        extractComments: false,
    },
    notifications: {
        onSuccess: false,
        onFailure: true
    }
});

mix.webpackConfig({
    stats: {
        children: false,
    },
});

mix.alias({
    '@': path.join(__dirname, 'sources/js'),
});

mix.js('sources/js/app.js', 'app.js');

mix.css('sources/css/app.pcss', 'app.css', [
    require('postcss-import'),
    require('tailwindcss/nesting'),
    require('tailwindcss'),
]);

mix.browserSync({
    proxy: 'https://craft.ddev.site',
    host: 'craft.ddev.site',

    ghostMode: false,
    notify: false,
    open: false,
    ui: false,

    https: {
        key: './.ddev/traefik/certs/craft.key',
        cert: './.ddev/traefik/certs/craft.crt',
    }
});
