<template>
    <div>
        <span>
            <b-form-checkbox @change="onChange" v-model="data[widget.name]" :disabled="widget.controllerAction == 'show' || !widget.fastChange || loading" />
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
                self.loading = true;
                axios.post(sanjabUrl('helpers/checkbox-widget/change'), {
                    controller: self.widget.controller,
                    action: self.widget.controllerAction,
                    item: self.widget.controllerItem,
                    widget: self.widget.name,
                    value: val,
                    item: self.data.id
                })
                .then(function (response) {
                    sanjabSuccessToast(sanjabTrans('updated_successfully'));
                    self.loading = false;
                    self.$forceUpdate();
                }).catch(function (error) {
                    self.loading = false;
                    console.error(error);
                    self.data[self.widget.name] = !self.data[self.widget.name];
                    sanjabHttpError(error.response.status);
                });
            }
        },
    }
</script>
