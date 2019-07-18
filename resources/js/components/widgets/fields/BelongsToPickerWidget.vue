<template>
    <div>
        <select-widget v-bind="allAttributes" :name="name" :options.sync="options" @search="onSearch" v-model="mutableValue" />
    </div>
</template>

<script>
    export default {
        props: {
            value: null,
            ajax: {
                type: Boolean,
                default: false
            },
            controller: {
                type: String,
                default: null
            },
            controllerAction: {
                type: String,
                default: null
            },
            controllerItem: {
                type: [String, Number],
                default: null
            },
            name: {
                type: String,
                default: null
            }
        },
        data() {
            return {
                mutableValue: null,
                options: [],
                searchTimeout: null
            };
        },
        mounted () {
            this.options = this.$attrs.options;
            this.mutableValue = this.value;
        },
        watch: {
            mutableValue(newValue, oldValue) {
                this.$emit("input", newValue);
            },
            value(newValue, oldValue) {
                this.mutableValue = newValue;
                this.onSearch(null, (b) => {});
            }
        },
        computed: {
            allAttributes() {
                if (this.ajax) {
                    var out = {};
                    for(var i in this.$attrs) {
                        if (i != 'options') {
                            out[i] = this.$attrs[i];
                        }
                    }
                    return out;
                }
                return this.$attrs;
            }
        },
        methods: {
            onSearch(search, loading) {
                var self = this;
                if (self.ajax == true) {
                    if (self.controller == null) {
                        return console.error('controller property for ajax is empty.');
                    }
                    if (self.searchTimeout) {
                        clearTimeout(self.searchTimeout);
                        self.searchTimeout = null;
                    }
                    self.searchTimeout = setTimeout(function () {
                        loading(true);
                        self.searchTimeout = null;
                        axios.post(sanjabUrl('helpers/relation-widgets/options'), {
                            selected: self.mutableValue,
                            controller: self.controller,
                            action: self.controllerAction,
                            name: self.name,
                            item: self.controllerItem,
                            search: search
                        })
                        .then(function (response) {
                            self.options = response.data;
                            loading(false);
                            self.$forceUpdate();
                        }).catch(function (error) {
                            console.error(error);
                        });
                    }, 500);
                }
            }
        },
    }
</script>
