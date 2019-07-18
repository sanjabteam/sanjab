<template>
    <b-row>
        <b-col cols="2">
            <b>{{ widget.title }}:</b>
        </b-col>
        <b-col cols="10">
            <component :is="widget.viewTag" :data="data" :widget="widget" />
        </b-col>
    </b-row>
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
    }
</script>
