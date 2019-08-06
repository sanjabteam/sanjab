<template>
    <div>
        <select-widget v-model="value" :options="options">
            <template slot="option" slot-scope="option">
                <i :class="option.value"></i>
                {{ option.label }}
            </template>
            <template slot="selected-option" slot-scope="option">
                <i :class="option.value"></i>
                {{ option.label }}
            </template>
        </select-widget>
    </div>
</template>

<script>
    export default {
        props: {
            value: null,
            options: {
                type: Array,
                default: () => []
            },
        },
        data() {
            return {
                mutableValue: null
            };
        },
        mounted () {
            this.mutableValue = this.value;
        },
        watch: {
            value (newValue, oldValue) {
                this.$emit("input", newValue);
            },
            mutableValue (newValue, oldValue) {
                this.value = newValue;
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import '~@fortawesome/fontawesome-free/css/all.css';

    i {
        padding-left: 10px;
    }
</style>
