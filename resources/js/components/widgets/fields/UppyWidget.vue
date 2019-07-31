<template>
    <div>
        <hr />
        <b-button v-if="! readonly" ref="uppyButton" class="mb-3" variant="primary" :disabled="max <= files.length">{{ sanjabTrans('upload') }}</b-button>
        <div ref="uppyContainer"></div>
        <b-row>
            <b-col v-for="(file, index) in files" :key="index" :cols="fileType(file.type) == 'image' ? 2 : 6">
                <div v-if="fileType(file.type) == 'image'">
                    <a :href="file.link ? file.link : file.value" target="_blank">
                        <b-img :src="file.preview" width="128" height="128" thumbnail fluid />
                    </a>
                    <b-button v-if="! readonly" class="uppy-image-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                </div>
                <div v-else-if="fileType(file.type) == 'audio'">
                    <audio controls>
                        <source :src="file.preview" :type="file.type">
                        Your browser does not support the audio element.
                    </audio>
                    <b-button v-if="! readonly" class="uppy-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                </div>
                <div v-else-if="fileType(file.type) == 'video'">
                    <video controls>
                        <source :src="file.preview" :type="file.type">
                        Your browser does not support the video element.
                    </video>
                    <b-button v-if="! readonly" class="uppy-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    const Uppy = require("@uppy/core");
    const Dashboard = require("@uppy/dashboard");
    const Webcam = require("@uppy/webcam");
    const Url = require('@uppy/url');
    const Tus = require("@uppy/tus");

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
                default: ["image/*", "video/*", "audio/*"]
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
            }
        },
        mounted() {
            var self = this;
            this.uppy = Uppy({
                autoProceed: true,
                restrictions: {
                    maxFileSize: this.maxSize * 1024,
                    maxNumberOfFiles: 1,
                    minNumberOfFiles: this.min,
                    allowedFileTypes: this.mimeTypes
                },
                })
                .use(Dashboard, {
                    trigger: this.$refs.uppyButton,
                    target: this.$refs.uppyContainer,
                    inline: false,
                    replaceTargetContent: true,
                    showLinkToFileUploadResult: false,
                    showProgressDetails: true,
                    width: '100%',
                    browserBackButtonClose: true
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
            removeFile(index) {
                this.files.splice(index, 1);
                this.$emit("input", this.files);
            },
            fileType(type) {
                if (type.match(/image\/.*/)) {
                    return 'image';
                } else if (type.match(/video\/.*/)) {
                    return 'video';
                } else if (type.match(/audio\/.*/)) {
                    return 'audio';
                }
            },
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

<style lang="scss" scoped>
    .btn.uppy-remove-button {
        bottom: 20px;
    }

    .btn.uppy-image-remove-button {
        position: absolute;
        display: inline-block;
        bottom: 10px;
    }

    html[dir="rtl"] {
        .btn.uppy-image-remove-button {
            right: 38px;
        }
    }

    html[dir="ltr"] {
        .btn.uppy-image-remove-button {
            left: 38px;
        }
    }

</style>
