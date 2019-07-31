<template>
    <div>
        <b-card v-for="(item, itemIndex) in value" :key="itemIndex">
            <b-row>
                <b-col v-for="(viewWidget, index) in widget.widgets" :key="index" :cols="viewWidget.cols">
                    <component :is="viewWidget.viewGroupTag" :widget="viewWidget" :data="item" />
                </b-col>
            </b-row>
        </b-card>
        <hr />
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
            value() {
                return this.data[this.widget.name];
            }
        },
    }
</script>
