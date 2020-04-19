import { ImageUpload } from 'quill-image-upload';
import * as VueChartJs from 'vue-chartjs';
import store from './store';

function chartJsComponent(classType) {
    return {
        mixins: [classType, VueChartJs.mixins.reactiveProp],
        template: "",
        props: {
            options: {
                type: Object,
                default: null
            }
        },
        mounted () {
            this.renderChart(this.chartData, this.options)
        }
    };
}

var SanjabPlugin = {store};
SanjabPlugin.install = function (Vue, options) {
    Quill.register('modules/imageUpload', ImageUpload);
    Quill.register(Quill.import('attributors/class/color'), true);
    Quill.register(Quill.import('attributors/style/size'), true);
    Quill.register(Quill.import('attributors/style/align'), true);

    Vue.use(require('bootstrap-vue').default);
    Vue.use(require('vue-quill-editor').default);

    Vue.prototype.sanjabTrans = sanjabTrans;
    Vue.prototype.numberFormat = numberFormat;
    Vue.prototype.$sanjabStore = store;

    Vue.component('vue-bootstrap-typeahead', require('vue-bootstrap-typeahead').default);
    Vue.component('tags-input', require('@voerro/vue-tagsinput').default);

    let chartTypes = {'bar-chart': VueChartJs.Bar,'horizontal-bar-chart': VueChartJs.HorizontalBar,'doughnut-chart': VueChartJs.Doughnut,'line-chart': VueChartJs.Line,'pie-chart': VueChartJs.Pie,'polar-area-chart': VueChartJs.PolarArea,'radar-chart': VueChartJs.Radar,'bubble-chart': VueChartJs.Bubble,'scatter-chart': VueChartJs.Scatter};

    for (let i in chartTypes) {
        Vue.component(i, chartJsComponent(chartTypes[i]));
    }

    const files = require.context('./components/', true, /\.vue$/i);
    files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
}
export default SanjabPlugin;
