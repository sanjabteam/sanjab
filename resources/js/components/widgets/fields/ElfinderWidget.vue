<template>
    <div>
        <hr />
        <b-button v-if="! readonly" @click="onFileSelect" ref="selectButton" class="mb-3" variant="primary" :disabled="max <= files.length">{{ sanjabTrans('select_file') }}</b-button>
        <file-preview v-model="files" :readonly="readonly" @onRemove="$emit('input', files)" />
    </div>
</template>

<script>

    export default {
        data() {
            return {
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
            },
            disk: {
                type: String,
                default: "public"
            }
        },
        mounted() {
            var self = this;
            this.files = this.validValues(this.value);
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
            },
            onFileSelect() {
                var self = this;
                var iframe = '<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe id="filemanager_iframe" src="'
                                + sanjabUrl('filemanager?popup=true&maxsize='+ self.maxSize.toString() + '&max=' + (self.max - self.files.length) + '&disk=' + encodeURIComponent(self.disk) +(self.multiple ? '&multiple=true' : ''))
                                + '" style="height:calc(100% - 4px);width:calc(100% - 4px)"></iframe><script>document.getElementById("filemanager_iframe").contentWindow.onmessage = function (e) {window.postMessage(e.data, "*");}<\/script></html></body>';
                var popup = window.open("", "", ((window.innerWidth > 640 && window.innerHeight > 480) ? 'width=' + 640 + ',height=' + 480 : ''));
                popup.document.write(iframe);
                popup.onmessage = function (e) {
                    if (typeof e.data === 'object' && e.data.type == 'sanjab-elfinder-file-selected' && typeof e.data.files == 'object' ) {
                        var files = e.data.files;
                        if (self.multiple == false) {
                            self.files = [];
                        }
                        for (var i in files) {
                            var path = files[i].path.replace(/\\/g, '/').replace(/^[^\/]+\//g, '');
                            var url = sanjabUrl('helpers/uppy/preview?path=' + encodeURIComponent(path) + '&disk=' + encodeURIComponent(self.disk));
                            self.files.push({type: files[i].mime, link: url, preview: url + '&thumb=true', value: path});
                        }
                        self.$emit("input", self.files);
                    }
                };
                window.addEventListener("beforeunload", function () {
                    if (popup) {
                        popup.close();
                    }
                });
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.files = this.validValues(newValue);
            }
        },
    };
</script>

