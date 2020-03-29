<template>
    <div>
        <uppy-widget :value="data[widget.name]" v-bind="widget" :readonly="true" />
    </div>
</template>

<script>
    export default {
        props: {
            widget: {
                type: Object,
                default: () => {return {};}
            },
            data: {
                type: Object,
                default: () => {return {};}
            }
        },
        computed: {
            val() {
                var out = this.widget.options.filter((opt) => { return opt.value == this.data[this.widget.name] });
                if (out.length > 0) {
                    return out[0].label;
                }
                return null;
            }
        },
    }
</script>
