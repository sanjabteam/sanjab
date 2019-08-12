import Quill from 'quill';
import { ImageUpload } from 'quill-image-upload';

var SanjabPlugin = {};
SanjabPlugin.install = function (Vue, options) {
    Quill.register('modules/imageUpload', ImageUpload);
    Quill.register(Quill.import('attributors/class/color'), true);
    Quill.register(Quill.import('attributors/style/size'), true);
    Quill.register(Quill.import('attributors/style/align'), true);

    Vue.use(require('bootstrap-vue').default);
    Vue.use(require('vue-quill-editor').default);

    Vue.prototype.sanjabTrans = sanjabTrans;

    const files = require.context('./components/', true, /\.vue$/i);
    files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

    Vue.component('vue-bootstrap-typeahead', require('vue-bootstrap-typeahead').default);
    Vue.component('tags-input', require('@voerro/vue-tagsinput').default);
}
export default SanjabPlugin;
