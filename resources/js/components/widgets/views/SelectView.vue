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
                default: () => {return {};}
            },
            data: {
                type: Object,
                default: () => {return {};}
            }
        },
        computed: {
            val() {
                var value = this.data[this.widget.name];
                if (! (value instanceof Array)) {
                    value = [value];
                }
                return this.widget.options.filter((opt) => value.includes(opt.value)).map((opt) => opt.label).join(',');
            }
        },
    }
</script>
