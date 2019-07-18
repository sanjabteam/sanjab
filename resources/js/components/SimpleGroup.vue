<template>
    <div>
        <div v-if="widget.floatlabel === true" class="form-group bmd-form-group">
            <label class="bmd-label-floating">{{ widget.title }}</label>
            <component :is="widget.tag" v-model="mutableValue" v-bind="widget"></component>
            <div v-if="fieldError" class="invalid-feedback d-block">
                {{ fieldError }}
            </div>
            <small v-else tabindex="-1" class="form-text text-muted">{{ widget.description }}</small>
        </div>
        <b-form-group v-else :label="widget.title" :label-for="widget.name" :description="widget.description" :state="fieldError && fieldError.length > 0 ? false : true" :invalid-feedback="fieldError">
            <component :is="widget.tag" v-model="mutableValue" v-bind="widget">{{ widget.content ? widget.content : '' }}</component>
        </b-form-group>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                mutableValue: null
            };
        },
        props: {
            value: null,
            widget: {
                type: Object,
                default: () => {}
            },
            properties: {
                type: Object,
                default: () => {}
            },
            errors: {
                type: Object,
                default: () => {}
            },
            fieldLocale: {
                type: String,
                default: null
            }
        },
        watch: {
            value:{
                handler(newValue, oldValue) {
                    this.mutableValue = newValue;
                },
                deep: true
            },
            mutableValue: {
                handler(newValue, oldValue) {
                    this.$emit("input", newValue);
                },
                deep: true
            }
        },
        computed: {
            fieldError () {
                var widgetName = this.widget.name;
                if (this.fieldLocale) {
                    widgetName = 'sanjab_translations.' + this.fieldLocale + '.' + widgetName;
                }
                return this.errors[widgetName] ?
                        Object.values(this.errors[widgetName])[0] :
                        (
                            this.errors[widgetName + '.*'] ?
                            Object.values(this.errors[widgetName + '.*'])[0] :
                            null
                        );
            }
        }
    }
</script>
