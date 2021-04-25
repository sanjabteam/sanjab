<template>
    <div>
        <hr v-if="!withoutUi" />
        <b-button v-if="!withoutUi && !readonly" ref="uppyButton" class="mb-3" variant="primary" :disabled="max <= files.length">{{ sanjabTrans('upload') }}</b-button>
        <slot v-if="withoutUi"></slot>
        <div ref="uppyContainer"></div>
        <file-preview v-if="!withoutUi" v-model="files" :readonly="readonly" @onRemove="$emit('input', files)" />
    </div>
</template>

<script>
    const Uppy = require("@uppy/core");
    const Dashboard = require("@uppy/dashboard");
    const Webcam = require("@uppy/webcam");
    const ImageEditor = require("@uppy/image-editor");
    const Tus = require("@uppy/tus");
    const UppyLocales = {
        "ar": require('@uppy/locales/src/ar_SA'),
        "de": require('@uppy/locales/src/de_DE'),
        "en": require('@uppy/locales/src/en_US'),
        "es": require('@uppy/locales/src/es_ES'),
        "fa": require('@uppy/locales/src/fa_IR'),
        "fi": require('@uppy/locales/src/fi_FI'),
        "fr": require('@uppy/locales/src/fr_FR'),
        "hu": require('@uppy/locales/src/hu_HU'),
        "it": require('@uppy/locales/src/it_IT'),
        "ja": require('@uppy/locales/src/ja_JP'),
        "nl": require('@uppy/locales/src/nl_NL'),
        "pt": require('@uppy/locales/src/pt_BR'),
        "ru": require('@uppy/locales/src/ru_RU'),
        "tr": require('@uppy/locales/src/tr_TR'),
        "zn": require('@uppy/locales/src/zh_CN'),
    };

    export default {
        data() {
            return {
                uppy: null,
                files: []
            }
        },
        props: {
            max: {
                type: Number,
                default: Number.MAX_SAFE_INTEGER
            },
            min: {
                type: Number,
                default: 0
            },
            maxSize: {
                type: Number,
                default: Number.MAX_SAFE_INTEGER
            },
            mimeTypes: {
                type: Array,
                default: () => ["image/*", "video/*", "audio/*"]
            },
            multiple: {
                type: Boolean,
                default: false
            },
            value: {
                type: Array,
                default: () => []
            },
            readonly: {
                type: Boolean,
                default: false
            },
            withoutUi: {
                type: Boolean,
                default: false
            },
            imageEditor: {
                type: Boolean,
                default: false
            },
            cropperOptions: {
                type: Object,
                default: () => {}
            },
        },
        mounted() {
            var self = this;
            this.uppy = Uppy({
                autoProceed: !self.imageEditor,
                locale: (UppyLocales[document.documentElement.lang] != undefined) ? UppyLocales[document.documentElement.lang] : UppyLocales['en'],
                restrictions: {
                    maxFileSize: this.maxSize * 1024,
                    maxNumberOfFiles: 1,
                    minNumberOfFiles: this.min,
                    allowedFileTypes: this.mimeTypes
                },
                })
                .use(Dashboard, {
                    trigger: this.withoutUi ? this.$slots.default[0].elm : this.$refs.uppyButton,
                    target: this.$refs.uppyContainer,
                    inline: false,
                    replaceTargetContent: true,
                    showLinkToFileUploadResult: false,
                    showProgressDetails: true,
                    width: '100%',
                    browserBackButtonClose: true
                })
                .use(ImageEditor, {
                    target: Dashboard,
                    cropperOptions: this.cropperOptions,
                })
                .use(Tus, {
                    endpoint: sanjabUrl("helpers/uppy/upload"),
                    limit: 1,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .use(Webcam, {
                    target: Dashboard,
                })
                .on("upload-success", function (file, response) {
                    if (self.multiple) {
                        self.files.push({type: file.type, preview: response.uploadURL + '?thumb=true', value: response.uploadURL});
                    } else {
                        self.files = [{type: file.type, preview: response.uploadURL + '?thumb=true', value: response.uploadURL}];
                    }
                    self.$emit("input", self.files);
                })
                .on("dashboard:modal-open", function () {
                    if (self.multiple) {
                        self.uppy.opts.restrictions.maxNumberOfFiles = self.max - self.files.length;
                    }
                })
                .on("dashboard:modal-closed", function () {
                    self.uppy.reset();
                })
                .on('file-added', function (file) {
                    self.uppy.setFileMeta(file.id, { name: file.name.replace(/[@#$%^&*\(\)\s]/g, '_') });
                });

            this.files = this.validValues(this.value);
        },
        beforeDestroy () {
            delete this.uppy;
        },
        methods: {
            validValues(values) {
                var newFiles = [];
                if (values instanceof Array) {
                    for (var i in values) {
                        if (values[i].preview != undefined && values[i].value != undefined && values[i].type != undefined) {
                            newFiles.push(values[i]);
                        }
                    }
                }
                return newFiles;
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.files = this.validValues(newValue);
            }
        },
    };
</script>

