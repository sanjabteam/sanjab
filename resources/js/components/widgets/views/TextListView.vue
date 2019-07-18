<template>
    <div>
        <b-table striped hover :items="tableItems" :fields="fields" thead-class="d-none">
        </b-table>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: () => {}
            },
            widget: {
                type: Object,
                default: () => {}
            },
        },
        data() {
            return {
                fields: [
                    {
                        key: 'name',
                        sortable: false
                    },
                ]
            }
        },
        computed: {
            tableItems() {
                var out = [];
                if (this.data[this.widget.name] instanceof Array) {
                    for (var i in this.data[this.widget.name]) {
                        out.push({name: this.data[this.widget.name][i]});
                    }
                }
                return out;
            }
        },
    }
</script>
