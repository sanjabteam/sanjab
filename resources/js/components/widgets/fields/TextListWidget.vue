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
        <b-table striped hover responsive :items="tableItems" :fields="fields" thead-class="d-none">
            <template v-slot:cell(delete)="data">
                <b-button-group>
                    <b-button @click="removeOption(data.index)" variant="danger" size="sm" :title="sanjabTrans('delete')" v-b-tooltip><i class="material-icons">delete</i></b-button>
                </b-button-group>
            </template>
        </b-table>
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
            }
        },
        data() {
            return {
                newItem: "",
                items: [],
                fields: [
                    {
                        key: 'name',
                        sortable: false
                    },
                    {
                        key: 'delete',
                        sortable: false
                    },
                ]
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
                        this.items.unshift(this.newItem);
                        this.newItem = "";
                        this.$emit("input", this.items);
                    }
                }
            },
            removeOption(index) {
                this.items.splice(index, 1);
                this.$emit("input", this.items);
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.items = newValue;
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
