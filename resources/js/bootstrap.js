window._ = require('lodash');

try {
    window.Vue = require('vue');
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
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
    window.Quill = require('quill');
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

require('./material-dashboard');
require('./scripts');
Vue.use(require('./plugin').default);

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (document.querySelector('#sanjab_navbar_app')) {
    window.sanjabSearchApp = new Vue({
        el: '#sanjab_navbar_app',
    });
}

if (document.querySelector('#sanjab_navbar_mobile_app')) {
    window.sanjabSearchApp = new Vue({
        el: '#sanjab_navbar_mobile_app',
    });
}
