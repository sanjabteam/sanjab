<template>
    <div>
        <span v-if="typeof widget.showHtml != 'undefined' && widget.showHtml" v-html="data[widget.name]">
        </span>
        <span v-else>
            {{ data[widget.name] }}
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
    }
</script>
