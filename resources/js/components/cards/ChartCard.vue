<template>
    <component :is="borderless ? 'div' : 'b-card'">
        <component v-if="show" :is="chartTag" :options="options" :chart-data="chartdata" :height="height">
        </component>
    </component>
</template>

<script>
    export default {
        props: {
            chartTag: {
                type: String,
                default: 'bar-chart'
            },
            labels: {
                type: Array,
                default: () => ['Please define labels']
            },
            data: {
                type: Object,
                default: null
            },
            height: {
                type: Number,
                default: 400
            },
            title: {
                type: [String, null],
                default: null
            },
            borderless: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                show: false
            };
        },
        mounted () {
            var self = this;
            self.show = !$('body').hasClass('screen-saver');
            $(document).on('sanjab-screen-saver-closed', function () {
                self.show = !$('body').hasClass('screen-saver');
            });
        },
        computed: {
            chartdata() {
                return {
                    labels: this.labels,
                    datasets: this.data instanceof Object && this.data.data != undefined ? this.data.data : []
                };
            },
            options() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: this.title != null,
                        text: this.title
                    },
                    tooltips: {
                        mode: 'index'
                    }
                };
            }
        },
    }
</script>
