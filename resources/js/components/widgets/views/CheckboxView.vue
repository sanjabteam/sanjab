<template>
    <div>
        <span>
            <b-form-checkbox @change="onChange" v-model="data[widget.name]" :disabled="! widget.changable && !loading" />
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            widget: {
                type: Object,
                default: () => {}
            },
            data: {
                type: Object,
                default: () => {}
            },
        },
        data() {
            return {
                loading: false
            }
        },
        methods: {
            onChange(val) {
                var self = this;
                axios.post(route("admin.helpers.change_checkbox"), {
                    model: self.widget.changableModel,
                    field: self.widget.name,
                    id: self.data.id,
                    value: val,
                    timestamps: false
                })
                .then(function (response) {
                    swal({
                        toast: true,
                        position: 'bottom-start',
                        showConfirmButton: false,
                        timer: 3000,
                        type: 'success',
                        title: 'با موفقیت تغییر یافت'
                    });
                    self.$forceUpdate();
                }).catch(function (error) {
                    console.error(error);
                });
            }
        },
    }
</script>
