<template>
    <b-row>
        <b-col v-for="(card, index) in cards" :key="index" :cols="card.cols">
            <component :is="card.tag" :data="typeof data[index] != 'undefined' ? data[index] : null" v-bind="card" />
        </b-col>
    </b-row>
</template>

<script>
    export default {
        props: {
            cards: {
                type: Array,
                default: () => []
            },
            data: {
                type: Array,
                default: () => []
            }
        },
    }
</script>
