<template>
    <div>
        <div class="sanjab-color-view" :style="{'background-color': data[widget.name]}">
            <span>{{ data[widget.name] }}</span>
        </div>
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
        }
    }
</script>

<style lang="scss">
    .sanjab-color-view {
        display: block;
        padding: 5px;

        span {
            color: white;
            mix-blend-mode: difference;
        }
    }
</style>
