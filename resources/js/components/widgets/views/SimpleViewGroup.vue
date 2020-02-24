<template>
    <b-row class="sanjab-simple-view-group">
        <b-col cols="4">
            <b>{{ widget.title }}:</b>
        </b-col>
        <b-col cols="8">
            <component :is="widget.viewTag" :data="data" :widget="widget" :crud-type="crudType" />
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
            crudType: {
                type: String,
                default: "index"
            }
        },
    }
</script>
