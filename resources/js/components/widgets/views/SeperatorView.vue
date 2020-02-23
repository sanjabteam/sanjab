<template>
    <div class="sanjab-seperator-widget">
        <span>{{ widget ? widget.name : '' }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            widget: {
                type: Object,
                default: () => {}
            },
        },
    }
</script>
