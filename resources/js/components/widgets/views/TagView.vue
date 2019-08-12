<template>
    <div>
        {{ tags }}
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: () => {}
            },
            widget: {
                type: Object,
                default: () => {}
            },
        },
        computed: {
            tags() {
                if (this.data[this.widget.name] instanceof Array) {
                    return this.data[this.widget.name].map((tag) => tag.value).join(',');
                }
                return "";
            }
        },
    }
</script>
