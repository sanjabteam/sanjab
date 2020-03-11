<template>
    <form class="navbar-form">
        <div class="input-group no-border">
            <vue-bootstrap-typeahead
                v-model="search"
                :data.sync="items"
                :placeholder="sanjabTrans('search')"
                :serializer="s => s.search"
                :max-matches="50"
                @hit="onItemHit"
            >
                <template v-slot:suggestion="{ data }">
                    <small>
                        <i class="material-icons">{{ data.icon }}</i><span v-html="data.title.replace(new RegExp('(' + search + ')', 'g'), '<strong>$1</strong>')"></span>
                    </small>
                </template>
            </vue-bootstrap-typeahead>
            <button type="button" class="btn btn-white btn-round btn-just-icon">
                <b-spinner v-if="loading"></b-spinner>
                <i v-else class="material-icons">search</i>
                <div class="ripple-container"></div>
            </button>
        </div>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                search: "",
                items: [],
                searchTimer: null,
                loading: false,
            };
        },
        methods: {
            find() {
                var self = this;
                self.loading = true;
                axios.get(sanjabUrl('search'), {
                    params: {search: this.search}
                })
                .then(function (response) {
                    self.items = response.data;
                    self.loading = false;
                }).catch(function (error) {
                    console.error(error);
                    sanjabHttpError(error.response.status);
                    self.loading = false;
                });
            },
            onItemHit($event) {
                window.location.href = $event.url;
            }
        },
        watch: {
            search(newValue, oldValue) {
                var self = this;
                if (self.searchTimer != null) {
                    clearTimeout(self.searchTimer);
                    self.searchTimer = null;
                }
                self.searchTimer = setTimeout(function () {
                    self.find();
                    self.searchTimer = null;
                }, 700);
            }
        },
    }
</script>
