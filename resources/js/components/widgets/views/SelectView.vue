<template>
    <div>
        <span v-if="val === null">
            {{ widget.withNull }}
        </span>
        <span v-else>
            {{ val }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            widget: {
                type: Object,
                default: () => {}
            },
            data: {
                type: Object,
                default: () => {}
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
