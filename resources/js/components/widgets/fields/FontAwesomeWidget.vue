<template>
    <div>
        <select-widget v-model="mutableValue" :options="options" :multiple="multiple">
            <template v-slot:option="option">
                <i :class="option.value"></i>
                {{ option.label }}
            </template>
            <template v-slot:selected-option="option">
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
            multiple: {
                type: Boolean,
                default: false
            }
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
                this.mutableValue = newValue;
            },
            mutableValue (newValue, oldValue) {
                this.$emit("input", newValue);
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
