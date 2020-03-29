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
                default: () => {return {};}
            },
            widget: {
                type: Object,
                default: () => {return {};}
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
