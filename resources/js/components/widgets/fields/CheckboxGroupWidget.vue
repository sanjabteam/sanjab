<template>
    <div>
        <b-form-checkbox v-if="all && options.length > 1" v-model="allChecked" @change="onAllChange">{{ sanjabTrans('all') }}</b-form-checkbox>
        <b-form-checkbox-group v-model="mutableValue" :options="options" />
    </div>
</template>

<script>
    export default {
        props: {
            options: {
                type: Array,
                default: () => []
            },
            all: {
                type: Boolean,
                default: false
            },
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                mutableValue: [],
                allChecked: false
            }
        },
        mounted () {
            if (this.value instanceof Array) {
                this.mutableValue = this.value;
            }
        },
        methods: {
            onAllChange (value) {
                this.mutableValue = [];
                if (value) {
                    for (var i in this.options) {
                        if (typeof this.options[i] != "function") {
                            this.mutableValue.push(this.options[i].value);
                        }
                    }
                }
            }
        },
        watch: {
            mutableValue (newValue, oldValue) {
                if (newValue.length == this.options.length) {
                    this.allChecked = true;
                } else {
                    this.allChecked = false;
                }
                this.$emit("input", newValue);
            },
            value (newValue, oldValue) {
                this.mutableValue = newValue;
            }
        },
    }
</script>
