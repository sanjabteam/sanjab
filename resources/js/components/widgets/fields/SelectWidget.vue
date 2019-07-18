<template>
    <div>
        <v-select ref="vueSelect" v-model="mutableValue" :multiple="multiple" :dir="dir" :options="options" v-on="$listeners" v-bind="$attrs"/>
    </div>
</template>

<script>
    export default {
        components: {
            'v-select': require('vue-select').default
        },
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
        created () {
            if (this.multiple) {
                this.mutableValue = [];
            }
        },
        mounted () {
            this.mutableValue = this.value;
        },
        watch: {
            mutableValue (newValue, oldValue) {
                if (newValue instanceof Array) {
                    var value = [];
                    for (var i in newValue) {
                        value.push(newValue[i].value);
                    }
                    if (value.filter((i) => !this.value.includes(i)).length != 0) {
                        this.$emit("input", value);
                    }
                } else {
                    this.$emit("input", newValue.value);
                }
            },
            value (newValue, oldValue) {
                this.mutableValue = this.mapValue(newValue);
                this.$forceUpdate();
            },
            options (newValue, oldValue) {
                this.mutableValue = [];
                this.mutableValue = this.mapValue(this.value);
            }
        },
        computed: {
            dir() {
                return document.dir;
            }
        },
        methods: {
            mapValue(newValue) {
                var value = null;
                if (this.multiple) {
                    value = [];
                    for (var i in this.options) {
                        if (newValue.includes(this.options[i].value)) {
                            value.push(this.options[i]);
                        }
                    }
                } else {
                    for (var i in this.options) {
                        if (this.options[i].value == newValue) {
                            value = this.options[i];
                        }
                    }
                }
                return value;
            }
        },
    }
</script>
