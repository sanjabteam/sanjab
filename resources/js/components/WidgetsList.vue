<template>
    <div>
        <cards-list :cards="cards" :data="cardsData" />
        <b-row>
            <b-col md="5" class="my-1">
                <b-button v-for="(action, index) in generalActions" @click="onActionClick(action)" :variant="action.variant" :href="action.url ? action.url : 'javascript:void(0);'" :key="index" :title="action.title" :target="action.target" v-b-tooltip><i class="material-icons">{{ action.icon }}</i>{{ action.title }}</b-button>
            </b-col>
            <b-col md="5" class="my-1">
                <b-input-group v-if="properties.searchable">
                    <b-form-input @keyup="onFilterChanged" :value="filter" :placeholder="sanjabTrans('search') + '...'" class="mt-1"></b-form-input>
                    <b-button-group>
                        <b-button v-b-toggle.advanced_search_collapse variant="primary" size="sm" :title="sanjabTrans('advanced_search')" v-b-tooltip>
                            <i class="material-icons">search</i>
                        </b-button>
                    </b-button-group>
                    <b-dropdown v-if="filterOptions.length > 0" variant="primary" size="sm" :title="sanjabTrans('filter')" v-b-tooltip no-caret>
                        <template v-slot:button-content>
                            <i class="material-icons">filter_list</i>
                        </template>
                        <b-dropdown-item :active="typeof filterOptions[filterOption] === 'undefined'" @click="changeFilterOption(null)">{{ sanjabTrans('all') }}</b-dropdown-item>
                        <b-dropdown-item v-for="(fOption, fOptionIndex) in filterOptions" :key="fOptionIndex" :active="filterOption == fOptionIndex" @click="changeFilterOption(fOptionIndex)">{{ fOption.title }}</b-dropdown-item>
                    </b-dropdown>
                </b-input-group>
            </b-col>
            <b-col md="2" class="my-1">
                <b-form-select v-model="perPage" :options="perPageOptions"></b-form-select>
            </b-col>
        </b-row>

        <!-- Search -->
        <b-collapse id="advanced_search_collapse">
            <b-form @submit.prevent="onSearch">
                <div v-for="(widget, index) in widgets" :key="index">
                    <b-row v-if="widget.searchTypes != null">
                        <b-col :sm="6" :md="2" class="my-4">
                            {{ widget.title }}
                        </b-col>
                        <b-col :sm="6" :md="2">
                             <b-form-select v-model="searchTypes[widget.name]" :options="searchTypeOptions(widget)"></b-form-select>
                        </b-col>
                        <b-col :sm="12" :md="8">
                            <b-row v-for="(searchType, searchIndex) in widget.searchTypes" :key="index + '_' + searchIndex" v-show="searchType.type == searchTypes[widget.name]">
                                <b-col v-for="(searchWidget, searchWidgetIndex) in searchType.widgets" :key="index + '_' + searchIndex + '_' + searchWidgetIndex">
                                    <component :is="searchWidget.groupTag" v-model="search[widget.name][searchType.type][searchWidget.name]" :widget="searchWidget" :properties="properties" />
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </div>
                <b-button type="submit" class="my-2" variant="primary" block>{{ sanjabTrans('search') }}</b-button>
            </b-form>
        </b-collapse>
        <!-- END search -->

        <!-- Bulk actions -->
        <b-collapse id="bulk_actions_collapse" v-model="bulkActionsVisible">
             <b-button-group>
                <b-button v-for="(action) in bulkActions" :key="action.index" :disabled="selectedBulk.filter((bulkItem) => bulkItem.__can[action.index]).length != selectedBulk.length" @click="onActionClick(action, selectedBulk)" :variant="action.variant" href="javascript:void(0);" :target="action.target" :title="action.title" v-b-tooltip>
                    <i class="material-icons">{{ action.icon }}</i>
                    {{ action.title }}
                </b-button>
            </b-button-group>
        </b-collapse>
        <!-- END Bulk actions -->

        <div ref="beforeTable"></div>
        <b-table
            ref="table"
            :items="items"
            :fields="fields"
            :current-page="page"
            :per-page="perPage"
            :filter="filter"
            class="sanjab-crud-table"
            striped
            hover
            responsive
            show-empty
        >
            <template #table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner variant="default" class="align-middle">
                    </b-spinner>
                </div>
            </template>
            <template #empty>
                <center>{{ sanjabTrans('there_are_no_records_to_show') }}</center>
            </template>
            <template #emptyfiltered>
                <center>{{ sanjabTrans('no_records_found') }}</center>
            </template>
            <template v-slot:cell(bulk)="row">
                <b-form-checkbox :id="'bulk_select_'+ row.index" v-model="selectedBulk" :value="row.item" />
            </template>
            <template v-slot:cell(actions)="row">
                <b-button-group>
                    <b-button v-for="(action) in perItemActions" :key="action.index" v-if="row.item.__can[action.index] == true" @click="onActionClick(action, row.item)" :variant="action.variant" :href="row.item.__action_url[action.index] ? row.item.__action_url[action.index] : 'javascript:void(0);'" :target="action.target" size="sm" :title="action.title" v-b-tooltip>
                        <i class="material-icons">{{ action.icon }}</i>
                    </b-button>
                </b-button-group>
            </template>

            <template v-for="tableColumn in tableColumns" v-slot:[tableColumn.slotName]="row" >
                <component :key="tableColumn.key" crud-type="index" :is="tableColumn.tag" :widget="tableColumn.widget" :data="row.item" />
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
        <b-modal ref="actionModal" :title="currentAction.title" :size="currentAction.modalSize" hide-footer>
            <component :is="currentAction.tag" :widgets="widgets" :item="this.actionItem" :items="this.actionItems" :properties="properties" v-bind="currentAction.tagAttributes">{{ currentAction.tagContent }}</component>
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
            filterOptions: {
                type: Array,
                default:() => []
            },
            cards: {
                type: Array,
                default:() => []
            },
            properties: {
                type: Object,
                default:() => {return {};}
            },
        },
        data() {
            return {
                page: 1,
                perPage: this.properties.perPage,
                total: 0,
                filter: "",
                filterOption: null,
                filterTimer: null,
                search: {},
                searchTypes: {},
                currentAction: {},
                actionItem: null,
                actionItems: null,
                selectedBulk: [],
                bulkActionsVisible: false,
                cardsData: [],
                autoRefreshing: false,
                autoRefreshingTimer: null,
                itemsCache: [],
            };
        },
        methods: {
            items(info, callback) {
                info.page = info.currentPage;
                info.searchTypes = this.searchTypes;
                info.filterOption = this.filterOption;
                info.search = this.search;
                info.autoRefreshing = this.autoRefreshing;
                info.total = this.total;
                var self = this;
                if (self.autoRefreshing) {
                    callback(self.itemsCache);
                } else if (self.autoRefreshingTimer) {
                    clearTimeout(self.autoRefreshingTimer);
                    self.autoRefreshingTimer = null;
                }
                axios.get(sanjabUrl("/modules/" + this.properties.route), {
                    params: info,
                    paramsSerializer: params => qs.stringify(params)
                })
                .then(function (response) {
                    if (response.data.notification != false) {
                        sanjabPlayNotificationSound();
                        if (typeof response.data.notification == 'string') {
                            sanjabToast(response.data.notification, {icon: 'info'});
                        }
                    }
                    self.cardsData = response.data.cardsData;
                    self.total = response.data.items.total;
                    self.autoRefreshing = false;
                    self.itemsCache = response.data.items.data;
                    callback(response.data.items.data);
                })
                .catch(function (error) {
                    console.error(error);
                    sanjabError(sanjabTrans('some_error_happend'));
                })
                .then(function () {
                    if (self.properties.autoRefresh > 0) {
                        self.autoRefreshingTimer = setTimeout(function () {
                            self.autoRefreshing = true;
                            self.$refs.table.refresh();
                        }, self.properties.autoRefresh * 1000);
                    }
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
            changeFilterOption(value) {
                this.filterOption = value;
                this.page = 1;
                this.$refs.table.refresh();
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
                } else {
                    if (item && !(item instanceof Array)) {
                        if (item.__action_url[action.index]) {
                            return;
                        }
                        item = [item];
                    }
                    if (action.action) {
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
                                    icon: 'success',
                                    title: result.value.message ? result.value.message : sanjabTrans('success'),
                                    confirmButtonText: action.confirmOk,
                                });
                                self.selectedBulk = [];
                                self.$refs.table.refresh();
                            }
                        })
                    } else if (action.url) {
                        let currentWindow = window;

                        // Url generating is based on a collection for bulk selection
                        if (action.bulkUrl) {
                            Swal.fire({
                                title: sanjabTrans('please_wait'),
                                timerProgressBar: true,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                            });
                            axios.post(sanjabUrl("/modules/" + self.properties.route + '/action/' + action.index), {
                                items : item ? item.map((it) => it.id) : null,
                            }).then(function (response) {
                                Swal.close();
                                if (response.data instanceof Array) {
                                    for (let i in response.data) {
                                        let newWidow = window.open(response.data[i], '_blank');
                                        if(!newWidow || newWidow.closed || typeof newWidow.closed == 'undefined') {
                                            sanjabError(sanjabTrans('to_open_multiple_urls_please_disable_popup_blocker_first'));
                                            return;
                                        }
                                        currentWindow.focus();
                                    }
                                } else {
                                    let newWidow = window.open(response.data, '_blank');
                                }
                            }).catch((error) => {
                                sanjabError(sanjabHttpErrorMessage(error.response.status));
                            });
                        } else {
                            for (let i in item) {
                                if (item[i].__action_url[action.index]) {
                                    let newWidow = window.open(item[i].__action_url[action.index], '_blank');
                                    if(!newWidow || newWidow.closed || typeof newWidow.closed == 'undefined') {
                                        sanjabError(sanjabTrans('to_open_multiple_urls_please_disable_popup_blocker_first'));
                                        return;
                                    }
                                    currentWindow.focus();
                                }
                            }
                        }
                    }
                }
            },
            onSearch() {
                this.filter = "";
                this.filterOption = null;
                this.$refs.table.refresh();
            },
            searchTypeOptions(widget) {
                if (typeof this.searchTypes[widget.name] === 'undefined') {
                    var newSearchTypeValue = {};
                    newSearchTypeValue[widget.name] = null;
                    this.searchTypes = Object.assign(newSearchTypeValue, this.searchTypes);
                    this.search[widget.name] = {};
                    for (var i in widget.searchTypes) {
                        this.search[widget.name][widget.searchTypes[i].type] = {};
                    }
                }
                return [{value: null, text: '---'}].concat(widget.searchTypes.map((stype) => {return {text: stype.title, value: stype.type}}));
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
                        return value.bulk == true && (value.action || value.url);
                    });
                }
                return [];
            },
            tableColumns() {
                var columns = [];
                for (var i in this.widgets) {
                    for (var j in this.widgets[i].tableColumns) {
                        this.widgets[i].tableColumns[j].widget = this.widgets[i];
                        this.widgets[i].tableColumns[j].slotName = "cell(" + this.widgets[i].tableColumns[j].key + ')';
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
            }
        },
  }
</script>
