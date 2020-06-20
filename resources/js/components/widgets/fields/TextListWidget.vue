<template>
    <div>
        <b-row>
            <b-col cols="9">
                <b-form-input ref="newItemInput" v-model="newItem" :placeholder="title" v-bind="inputOptions" @keydown.enter.prevent='addOption' />
            </b-col>
            <b-col cols="3">
                <b-button @click="addOption" :block="true" variant="success">{{ sanjabTrans('add') }}</b-button>
            </b-col>
        </b-row>
        <br>
        <b-table-simple striped hover responsive>
            <draggable v-model="items" tag="b-tbody" :disabled="! draggable" @end="onEnd">
                <tr v-for="(item, index) in items" :key="index" :style="{cursor: draggable ? 'move' : 'default'}">
                    <b-td>{{ item }}</b-td>
                    <b-td>
                        <b-button-group>
                            <b-button @click="removeOption(index)" variant="danger" size="sm" :title="sanjabTrans('delete')" v-b-tooltip.hover.left><i class="material-icons">delete</i></b-button>
                        </b-button-group>
                    </b-td>
                </tr>
            </draggable>
        </b-table-simple>
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
            title: {
                type: String,
                default: "Dyanmic item"
            },
            unique: {
                type: Boolean,
                default: false
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
                newItem: "",
                items: []
            }
        },
        methods: {
            addOption() {
                if (this.newItem.length > 0) {
                    if (!(this.items instanceof Array)) {
                        this.items = [];
                    }
                    if (this.$refs.newItemInput.reportValidity()) {
                        if (this.unique) {
                            if (this.items.map((txt) => txt.toLowerCase()).indexOf(this.newItem.toLowerCase()) != -1) {
                                sanjabError(sanjabTrans('this_item_added_before'));
                                return;
                            }
                        }
                        if (this.reversed) {
                            this.items.unshift(this.newItem);
                        } else {
                            this.items.push(this.newItem);
                        }
                        this.newItem = "";
                        this.$emit("input", this.items);
                    }
                }
            },
            removeOption(index) {
                var self = this;
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
                        }
                    });
                } else {
                    this.items.splice(index, 1);
                    this.$emit("input", this.items);
                }
            },
            onEnd() {
                this.$emit("input", this.items);
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.items = newValue;
            }
        }
    }
</script>
