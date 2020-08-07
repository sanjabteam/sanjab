<template>
    <div>
        <select-widget v-bind="allAttributes" :name="name" :options.sync="allOptions" @search="onSearch" v-model="mutableValue" />
    </div>
</template>

<script>
    export default {
        props: {
            value: null,
            creatable: null,
            creatableText: {
                type: String,
                default: sanjabTrans('create')
            },
            ajax: {
                type: Boolean,
                default: false
            },
            controller: {
                type: String,
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
                searchTimeout: null,
                searchText: '',
            };
        },
        mounted () {
            this.options = this.$attrs.options;
            if (JSON.stringify(this.mutableValue) == JSON.stringify(this.value)) {
                this.onSearch(null, (b) => {});
            } else {
                this.mutableValue = this.value;
            }
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
            },
            allOptions() {
                let options = this.creatableOptions.concat(this.options);
                if (
                    this.creatable &&
                    this.searchText &&
                    this.searchText.trim().length > 0 &&
                    this.options.filter((opt) => opt.label == this.searchText.trim()).length == 0 &&
                    this.creatableOptions.filter((opt) => opt.value.value == this.searchText.trim()).length == 0) {
                    options.push({value: {create_new: true, value: this.searchText}, label: this.creatableText + ': ' + this.searchText});
                }
                return options;
            },
            creatableOptions() {
                var result = [];
                if (this.mutableValue instanceof Array) {
                    for (let i in this.mutableValue) {
                        if (this.mutableValue[i] instanceof Object && this.mutableValue[i].create_new == true) {
                            result.push({value: this.mutableValue[i], label: this.creatableText + ': ' + this.mutableValue[i].value});
                        }
                    }
                } else if (this.mutableValue instanceof Object && this.mutableValue.create_new == true) {
                    result.push({value: this.mutableValue, label: this.creatableText + ': ' + this.mutableValue.value});
                }
                return result;
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
                    loading(true);
                    self.searchTimeout = setTimeout(function () {
                        self.searchTimeout = null;
                        let selectValues = [];
                        if (self.mutableValue instanceof Array) {
                            for (let i in self.mutableValue) {
                                if (self.mutableValue[i] && !(self.mutableValue[i] instanceof Object)) {
                                    selectValues.push(self.mutableValue[i]);
                                }
                            }
                        } else if (self.mutableValue && !(self.mutableValue instanceof Object)) {
                            selectValues.push(self.mutableValue);
                        }
                        axios.post(sanjabUrl('helpers/relation-widgets/options'), {
                            selected: selectValues,
                            controller: self.controller,
                            widget: self.name,
                            search: search
                        })
                        .then(function (response) {
                            self.options = response.data;
                            self.searchText = search;
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
