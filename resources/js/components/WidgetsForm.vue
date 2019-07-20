<template>
    <div>
        <b-alert v-for="(error, index) in errors" :key="index" variant="danger" show>
            {{ Object.values(error)[0] }}
        </b-alert>
        <b-form @submit.prevent="onSubmit">
            <b-row>
                <b-col v-for="(widget, index) in nonTranslatableWidgets" :key="index" :cols="widget.cols">
                    <component v-if="showWidget(widget)" :is="readonly ? widget.viewGroupTag : widget.groupTag" :widget="widget" :properties="properties" :errors.sync="errors" :crud-type="item == null ? 'create' : 'edit'" v-model="form[widget.name]" :data="form" />
                </b-col>
            </b-row>

            <b-card v-if="translatableWidgets.length > 0" no-body>
                <b-row>
                    <b-col v-if="!readonly">
                        <b-tabs fill small card pills>
                            <b-tab @click="onShowTranslationButton" :title="mainLocale.name">
                                <b-row>
                                    <b-col v-for="(widget, index) in translatableWidgets" :key="'tr' + mainLocale.locale + '_' + index" :cols="widget.cols">
                                        <component v-if="showWidget(widget)" :is="widget.groupTag" :widget="widget" :properties="properties" :errors.sync="errors" :crud-type="item == null ? 'create' : 'edit'" :field-locale="mainLocale.locale" v-model="form.sanjab_translations[mainLocale.locale][widget.name]" />
                                    </b-col>
                                </b-row>
                            </b-tab>
                        </b-tabs>
                    </b-col>
                    <b-col id="translations_tab" v-show="readonly">
                        <b-tabs v-model="currentLocaleTab" fill small card pills>
                            <b-tab v-for="(localeName, locale) in locales" :key="locale" :title="localeName">
                                <b-row>
                                    <b-col v-for="(widget, index) in translatableWidgets" :key="'tr' + locale + '_' + index" :cols="widget.cols">
                                        <component v-if="showWidget(widget)" :is="readonly ? widget.viewGroupTag : widget.groupTag" :widget="widget" :properties="properties" :errors.sync="errors" :crud-type="item == null ? 'create' : 'edit'" :field-locale="locale" v-model="form.sanjab_translations[locale][widget.name]" :data="form.sanjab_translations[locale]" />
                                    </b-col>
                                </b-row>
                            </b-tab>
                        </b-tabs>
                    </b-col>
                </b-row>
            </b-card>

            <b-button v-if="!readonly" variant="success" type="submit" :disabled="loading">
                <b-spinner v-if="loading" small></b-spinner>
                {{ item == null ? sanjabTrans('create') : sanjabTrans('edit') }}
            </b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        props: {
            widgets: {
                type: Array,
                default: () => []
            },
            properties: {
                type: Object,
                default: () => {}
            },
            item: {
                type: Object,
                default: null
            },
            successUrl: {
                type: String,
                default: null
            },
            formUrl: {
                type: String,
                default: null
            },
            formMethod: {
                type: String,
                default: null
            },
            readonly: {
                type: Boolean,
                default: false
            }
        },
        created () {
            this.fixFormData();
        },
        mounted () {
            this.loadForm();
        },
        data() {
            return {
                form: {},
                errors: {},
                loading: true,
                currentLocaleTab: 0,
                showTranslation: false
            }
        },
        methods: {
            onSubmit() {
                var self = this;
                self.loading = true;
                axios({
                    method: self.formMethod ? self.formMethod : (self.item ? 'put' : 'post'),
                    url: self.formUrl ? self.formUrl : (self.item ? sanjabUrl('/modules/' + self.properties.route + '/' + self.item.id) : sanjabUrl('/modules/' + self.properties.route)),
                    data: self.form
                }).then(function (response) {
                    self.loading = false;
                    if (self.$listeners && self.$listeners.onSuccess) {
                        self.$emit('onSuccess');
                    } else if (self.successUrl) {
                        if (self.successUrl != window.location.href) {
                            window.location.href = self.successUrl;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.location.href = sanjabUrl('/modules/' + self.properties.route);
                    }
                }).catch(function (error) {
                    self.loading = false;
                    console.error(error);
                    if (error.response.status == 422) {
                        self.errors = error.response.data.errors;
                        $("body, html").animate({'scroll-top': 0})
                    } else {
                        sanjabHttpError(error.response.status);
                    }
                });
            },
            showWidget (widget) {
                return (this.readonly && widget.onView) || (this.readonly == false && ((this.item == null && widget.onCreate) || (this.item != null && widget.onEdit)));
            },
            loadForm() {
                if (this.item) {
                    this.form = this.item;
                    this.fixFormData();
                    this.loading = false;
                    this.$forceUpdate();
                    setTimeout(() => $(".bmd-form-group input, .bmd-form-group textarea").trigger("blur").trigger("change"), 50);
                } else {
                    this.loading = false;
                }
            },
            fixFormData() {
                if (typeof this.form.sanjab_translations !== 'object') {
                    this.form.sanjab_translations = {};
                    this.form.sanjab_translations[this.mainLocale.locale] = {};
                    for (var i in this.locales) {
                        this.form.sanjab_translations[i] = {};
                    }
                }
            },
            onShowTranslationButton() {
                if ($("#translations_tab").is(":visible")) {
                    $("#translations_tab").fadeOut();
                } else {
                    $("#translations_tab").fadeIn();
                }
            }
        },
        computed: {
            nonTranslatableWidgets() {
                return this.widgets.filter((widget) => widget.translation == false);
            },
            translatableWidgets() {
                return this.widgets.filter((widget) => widget.translation == true);
            },
            locales() {
                var locales = {};
                for (var i in window.sanjab.config.locales) {
                    if (i != this.mainLocale.locale || this.readonly) {
                        locales[i] = window.sanjab.config.locales[i];
                    }
                }
                return locales;
            },
            mainLocale() {
                if (typeof window.sanjab.config.locales[window.sanjab.app.locale] === 'undefined') {
                    return {locale: window.sanjab.config.locales.keys()[0], name: window.sanjab.config.locales[window.sanjab.config.locales.keys()[0]]};
                }
                return {locale: window.sanjab.app.locale, name: window.sanjab.config.locales[window.sanjab.app.locale]};
            }
        },
    }
</script>
