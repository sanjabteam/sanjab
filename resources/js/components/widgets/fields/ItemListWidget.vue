<template>
    <div>
        <b-button variant="success" type="button" @click="addOption" block>
            {{ sanjabTrans('add') }}
        </b-button>
        <b-card v-for="(item, itemIndex) in items" :key="itemIndex">
            <b-row>
                <b-col :cols="10">
                    <b-row>
                        <b-col v-for="(widget, index) in widgets" :key="index" :cols="widget.cols">
                            <component :is="widget.groupTag" :widget="widget" :properties="properties" :errors="widgetErrors(widget, itemIndex)" :crud-type="crudType" v-model="items[itemIndex][widget.name]" />
                        </b-col>
                    </b-row>
                </b-col>
                <b-col :cols="2">
                    <br>
                    <b-button @click="removeOption(itemIndex)" variant="danger" size="sm" :title="sanjabTrans('delete')" v-b-tooltip><i class="material-icons">delete</i></b-button>
                </b-col>
            </b-row>
        </b-card>
        <hr />
    </div>
</template>

<script>
    export default {
        props: {
            inputOptions: {
                type: Object,
                default: () => {}
            },
            value: {
                type: Array,
                default: () => []
            },
            widgets: {
                type: Array,
                default: () => []
            },
            name: {
                type: String,
                name: ""
            },
            title: {
                type: String,
                default: "Dyanmic"
            },
            unique: {
                type: Boolean,
                default: false
            },
            translation: {
                type: Boolean,
                default: false
            },
            properties: {
                type: Object,
                default: () => {}
            },
            crudType: {
                type: String,
                default: "create"
            },
            errors: {
                type: Object,
                default: () => {}
            }
        },
        data() {
            return {
                items: [],
                form: {},
            };
        },
        methods: {
            addOption() {
                this.items.unshift({});
                this.$emit("input", this.items);
                setTimeout(() => $(".bmd-form-group input").trigger('change'), 250);
            },
            removeOption(index) {
                this.items.splice(index, 1);
                this.$emit("input", this.items);
                setTimeout(() => $(".bmd-form-group input").trigger('change'), 250);
            },
            widgetErrors(widget, itemIndex) {
                var errors = {};
                var name = this.name;
                if (this.translation) {
                    name = 'sanjab_translations\\.[^\\.]+\\.' + name;
                }
                for (var i in this.errors) {
                    if (i.match(new RegExp(name + "\\." + itemIndex + "\\." + widget.name, "i"))) {
                        errors[i.replace(new RegExp(name + "\\.\\d\\."),'')] = this.errors[i];
                    }
                }
                return errors;
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.items = [];
                if (newValue instanceof Array) {
                    for (var i in newValue) {
                        if (newValue[i] instanceof Object && !(newValue[i] instanceof Array)) {
                            this.items.push(newValue[i]);
                        }
                    }
                    this.$forceUpdate();
                }
            }
        },
        computed: {
            tableItems() {
                var out = [];
                for (var i in this.items) {
                    out.push({name: this.items[i], delete:null});
                }
                return out;
            }
        },
    }
</script>
