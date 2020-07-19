<template>
    <div>
        <b-button v-if="max === null || items.length < max" variant="success" type="button" @click="addOption" block>
            {{ sanjabTrans('add') }}
        </b-button>
        <draggable v-model="items" :disabled="! draggable" @end="onEnd" handle=".draggable-handle">
            <b-card v-for="(item, itemIndex) in items" :key="itemIndex">
                <b-row>
                    <template v-for="(widget, index) in widgets">
                        <b-col v-if="showWidget(widget, item)" :key="index" :cols="widget.cols">
                            <component :is="widget.groupTag" :widget="widget" :properties="properties" :errors="widgetErrors(widget, itemIndex)" :crud-type="crudType" v-model="items[itemIndex][widget.name]" />
                        </b-col>
                    </template>
                </b-row>
                <b-button-group class="item-list-widget-buttons">
                    <b-button v-if="draggable" class="draggable-handle" style="cursor:move" variant="success" size="sm"><i class="material-icons">drag_handle</i></b-button>
                    <b-button @click="removeOption(itemIndex)" variant="danger" size="sm" :title="sanjabTrans('delete')" v-b-tooltip.hover.left><i class="material-icons">delete</i></b-button>
                </b-button-group>
            </b-card>
        </draggable>
        <hr />
    </div>
</template>

<script>
    export default {
        props: {
            inputOptions: {
                type: Object,
                default: () => {return {};}
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
            max: {
                type: Number,
                default: null
            },
            properties: {
                type: Object,
                default: () => {return {};}
            },
            crudType: {
                type: String,
                default: "create"
            },
            errors: {
                type: Object,
                default: () => {return {};}
            },
            deleteConfirm: {
                type: String,
                default: null
            },
            reversed: {
                type: Boolean,
                default: true,
            },
            draggable: {
                type: Boolean,
                default: false
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
                if (this.reversed) {
                    this.items.unshift({});
                } else {
                    this.items.push({});
                }
                this.$emit("input", this.items);
                setTimeout(() => $(".bmd-form-group input").trigger('change'), 250);
            },
            removeOption(index) {var self = this;
                if (this.deleteConfirm) {
                    Swal.fire({
                        title: self.deleteConfirm,
                        showCancelButton: true,
                        confirmButtonText: sanjabTrans('yes'),
                        cancelButtonText: sanjabTrans('no'),
                    }).then(function (result) {
                        if (result.value) {
                            self.items.splice(index, 1);
                            self.$emit("input", self.items);
                            setTimeout(() => $(".bmd-form-group input").trigger('change'), 250);
                        }
                    });
                } else {
                    this.items.splice(index, 1);
                    this.$emit("input", this.items);
                    setTimeout(() => $(".bmd-form-group input").trigger('change'), 250);
                }
            },
            onEnd() {
                this.$emit("input", this.items);
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
            },
            showWidget (widget, item) {
                return ((typeof item.__id === 'undefined' && widget.onCreate) || (typeof item.__id !== 'undefined' && widget.onEdit));
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
