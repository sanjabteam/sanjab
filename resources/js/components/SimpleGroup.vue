<template>
    <div>
        <div v-if="widget.floatlabel === true" class="form-group bmd-form-group">
            <label v-if="widget.hideGroupLabel === undefined || widget.hideGroupLabel == false" class="bmd-label-floating">{{ widget.title }}</label>
            <component :is="widget.tag" v-model="mutableValue" v-bind="widget"></component>
            <div v-if="fieldError" class="invalid-feedback d-block">
                {{ fieldError }}
            </div>
            <small v-else tabindex="-1" class="form-text text-muted">{{ widget.description }}</small>
        </div>
        <b-form-group v-else class="non-float-label-group" :label="widget.title" :label-for="widget.name" :label-sr-only="widget.hideGroupLabel === true" :description="widget.description" :state="fieldError && fieldError.length > 0 ? false : true" :invalid-feedback="fieldError">
            <component :is="widget.tag" :errors.sync="errors" v-model="mutableValue" v-bind="widget">{{ widget.content ? widget.content : '' }}</component>
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
                default: () => {return {};}
            },
            properties: {
                type: Object,
                default: () => {return {};}
            },
            errors: {
                type: Object,
                default: () => {return {};}
            },
            fieldLocale: {
                type: String,
                default: null
            }
        },
        mounted () {
            if (this.value) {
                this.mutableValue = this.value;
            } else if (this.widget.defaultValue !== undefined && this.widget.defaultValue !== null) {
                this.mutableValue = this.widget.defaultValue;
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
                if (this.errors instanceof Object && !(this.errors instanceof Array)) {
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
                return null;
            }
        }
    }
</script>

<style lang="scss">
    .non-float-label-group .bmd-label-static {
        top: 0.2rem !important;
    }
</style>
