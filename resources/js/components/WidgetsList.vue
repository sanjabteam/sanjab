<template>
    <div>
        <b-row>
            <b-col v-for="(card, index) in cards" :key="index" :cols="card.cols">
                <component :is="card.tag" :data="typeof cardsData[index] != 'undefined' ? cardsData[index] : null" v-bind="card" />
            </b-col>
        </b-row>
        <b-row ref="beforeTable">
            <b-col md="6" class="my-1">
                <b-button v-for="(action, index) in generalActions" @click="onActionClick(action)" :variant="action.variant" :href="action.url ? action.url : 'javascript:void(0);'" :key="index" :title="action.title" v-b-tooltip><i class="material-icons">{{ action.icon }}</i>{{ action.title }}</b-button>
            </b-col>
            <b-col md="4" class="my-1">
                <b-form-input @keyup="onFilterChanged" :placeholder="sanjabTrans('search') + '...'"></b-form-input>
            </b-col>
            <b-col md="2" class="my-1">
                <b-form-select v-model="perPage" :options="perPageOptions"></b-form-select>
            </b-col>
        </b-row>
        <b-collapse id="bulk_actions_collapse" v-model="bulkActionsVisible">
             <b-button-group>
                <div v-for="(action) in bulkActions" :key="action.index">
                    <b-button :disabled="selectedBulk.filter((bulkItem) => bulkItem.__can[action.index]).length != selectedBulk.length" @click="onActionClick(action, selectedBulk)" :variant="action.variant" href="javascript:void(0);" :title="action.title" v-b-tooltip>
                        <i class="material-icons">{{ action.icon }}</i>
                        {{ action.title }}
                    </b-button>
                </div>
            </b-button-group>
        </b-collapse>
        <b-table
            ref="table"
            :items="items"
            :fields="fields"
            :current-page="page"
            :per-page="perPage"
            :filter="filter"
            striped
            hover
            responsive
            show-empty
        >
            <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner variant="warning" class="align-middle"></b-spinner>
            </div>
            <template slot="empty">
                <center>{{ sanjabTrans('there_are_no_records_to_show') }}</center>
            </template>
            <template slot="emptyfiltered">
                <center>{{ sanjabTrans('no_records_found') }}</center>
            </template>
            <div slot="bulk" slot-scope="row">
                <b-form-checkbox :id="'bulk_select_'+ row.index" v-model="selectedBulk" :value="row.item" />
            </div>
            <div slot="actions" slot-scope="row">
                <b-button-group>
                    <div v-for="(action) in perItemActions" :key="action.index">
                        <b-button v-if="row.item.__can[action.index] == true" @click="onActionClick(action, row.item)" :variant="action.variant" :href="row.item.__action_url[action.index] ? row.item.__action_url[action.index] : 'javascript:void(0);'" size="sm" :title="action.title" v-b-tooltip>
                            <i class="material-icons">{{ action.icon }}</i>
                        </b-button>
                    </div>
                </b-button-group>
            </div>

            <template v-for="tableColumn in tableColumns" :slot="tableColumn.key" slot-scope="row">
                <component :key="tableColumn.key" :is="tableColumn.tag" :widget="tableColumn.widget" :data="row.item" />
            </template>
        </b-table>
        <b-pagination
            v-show="total/perPage > 1"
            v-model="page"
            :total-rows="total"
            :per-page="perPage"
            class="my-0"
            variant="warning"
            align="center"
        />
        <b-modal ref="actionModal" :title="currentAction.title" :size="currentAction.modalSize">
            <component :is="currentAction.tag" :widgets="widgets" :item="this.actionItem" :items="this.actionItems" :properties="properties" v-bind="currentAction.tagAttributes">{{ currentAction.tagContent }}</component>
            <div slot="modal-footer"></div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: {
            widgets: {
                type: Array,
                default:() => []
            },
            actions: {
                type: Array,
                default:() => []
            },
            cards: {
                type: Array,
                default:() => []
            },
            properties: {
                type: Object,
                default:() => {}
            },
        },
        data() {
            return {
                page: 1,
                perPage: this.properties.perPage,
                filter: "",
                total: 0,
                filterTimer: null,
                currentAction: {},
                actionItem: null,
                actionItems: null,
                selectedBulk: [],
                bulkActionsVisible: false,
                cardsData: []
            };
        },
        methods: {
            items(info) {
                info.page = info.currentPage;
                var self = this;
                return axios.get(sanjabUrl("/modules/" + this.properties.route), {
                    params: info
                })
                .then(function (response) {
                    self.cardsData = response.data.cardsData;
                    self.total = response.data.items.total;
                    return response.data.items.data;
                })
                .catch(function (error) {
                    console.error(error);
                });
            },
            onFilterChanged(event) {
                if (this.filterTimer) {
                    clearTimeout(this.filterTimer);
                    this.filterTimer = null;
                }
                var self = this;
                this.filterTimer = setTimeout(function () {
                    self.filter = event.target.value;
                    self.page = 1;
                    self.filterTimer = null;
                }, 700);
            },
            onActionClick(action, item = null) {
                var self = this;
                this.actionItem = null;
                this.actionItems = null;
                if (action.tag) {
                    if (typeof item instanceof Array) {
                        this.actionItems = item;
                    } else if (item) {
                        this.actionItem = item;
                    }
                    this.currentAction = action;
                    this.$refs.actionModal.show();
                } else if (action.action) {
                    if (item && !(item instanceof Array)) {
                        item = [item];
                    }
                    Swal.fire({
                        title: action.confirm,
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        confirmButtonText: action.confirmYes,
                        cancelButtonText: action.confirmNo,
                        input: action.confirmInput,
                        inputPlaceholder: action.confirmInputTitle,
                        inputAttributes: action.confirmInputAttributes,
                        preConfirm: (input) => {
                            return axios.post(sanjabUrl("/modules/" + self.properties.route + '/action/' + action.action), {
                                items : item ? item.map((it) => it.id) : null,
                                input: input
                            }).then(function (response) {
                                return response.data;
                            }).catch((error) => {
                                Swal.showValidationMessage(error.response.data.message ? error.response.data.message : sanjabHttpErrorMessage(error.response.status));
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then(function (result) {
                        if (result.value) {
                            Swal.fire({
                                type: 'success',
                                title: result.value.message ? result.value.message : sanjabTrans('success'),
                                confirmButtonText: action.confirmOk,
                            });
                            self.selectedBulk = [];
                            self.$refs.table.refresh();
                        }
                    })
                }
            }
        },
        computed: {
            fields() {
                var out = [];
                if (this.bulkActions.length > 0) {
                    out = out.concat({label: '✅', key: 'bulk', sortable:false});
                }
                for (var i in this.widgets) {
                    out = out.concat(this.widgets[i].tableColumns);
                }
                if (this.perItemActions.length > 0) {
                    out = out.concat({label: sanjabTrans('actions'), key: 'actions', sortable:false});
                }
                return out;
            },
            perPageOptions() {
                var out = [];
                for (var i in this.properties.perPages) {
                    out.push({value: i, text:  this.properties.perPages[i]});
                }
                return out;
            },
            generalActions() {
                return this.actions.map(function (value, index) {
                    value.index = index;
                    return value;
                }).filter(function (value) {
                    return value.perItem == false;
                });
            },
            perItemActions() {
                return this.actions.map(function (value, index) {
                    value.index = index;
                    return value;
                }).filter(function (value) {
                    return value.perItem == true;
                });
            },
            bulkActions() {
                if (this.properties.bulk) {
                    return this.perItemActions.filter(function (value) {
                        return value.bulk == true && value.action;
                    });
                }
                return [];
            },
            tableColumns() {
                var columns = [];
                for (var i in this.widgets) {
                    for (var j in this.widgets[i].tableColumns) {
                        this.widgets[i].tableColumns[j].widget = this.widgets[i];
                        columns.push(this.widgets[i].tableColumns[j]);
                    }
                }
                return columns;
            }
        },
        watch: {
            page: function () {
                $(this.$refs.beforeTable)[0].scrollIntoView();
            },
            selectedBulk: {
                deep: true,
                handler: function (newValue, oldValue) {
                    if (newValue.length > 0) {
                        this.bulkActionsVisible = true;
                    } else {
                        this.bulkActionsVisible = false;
                    }
                }
            },
        },
  }
</script>
