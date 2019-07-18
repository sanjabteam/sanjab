var mix = require('laravel-mix');

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}

mix.setPublicPath('resources/assets')
    .setResourceRoot('/vendor/sanjab')
    .js('resources/js/sanjab.js', 'resources/assets/js')
    .sass('resources/sass/sanjab.scss', 'resources/assets/css');
