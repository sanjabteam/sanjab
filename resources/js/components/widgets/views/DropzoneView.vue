<template>
    <div>
        <div v-if="images.length > 0">
            <a v-for="(image, index) in images" :key="index" :href="image.original" target="_blank">
                <img :src="image.url" />
            </a>
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
        },
        computed: {
            images() {
                var out = this.data[this.widget.name];
                if (out === undefined || out === null) {
                    out = [];
                } else if (!(out instanceof Array)) {
                    out = [out];
                }
                return out;
            }
        },
    }
</script>
