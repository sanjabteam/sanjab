window._ = require('lodash');

try {
    window.Vue = require('vue');
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    window.PerfectScrollbar = require('perfect-scrollbar').default;
    window.moment = require('moment');
    window.qs = require('qs');
    window.Swal = require('sweetalert2').mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false,
    });
    window.require = window.requirejs = require('shebang-loader!requirejs');
    window.ace = require('ace-builds');
    window.define = ace.define;
    require('ace-builds/src-noconflict/theme-monokai');
    ace.config.set(
        "basePath",
        "https://cdn.jsdelivr.net/npm/ace-builds@1.4.5/src-noconflict/"
    );

    require('bootstrap');
    require('bootstrap-material-design');
    require('jquery-ui-bundle');
} catch (e) {
    console.error(e);
}

Vue.use(require('./plugin').default);
require('./material-dashboard');
require('./scripts');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

if (document.querySelector('#sanjab_search_app')) {
    window.sanjabSearchApp = new Vue({
        el: '#sanjab_search_app',
    });
}
