<template>
    <div>
        <vue-dropzone
            ref="dropzoneInstance"
            id="test"
            :options="dropzoneOptions"
            @vdropzone-success="onSuccess"
            @vdropzone-removed-file="onRemove"
            @vdropzone-processing="onProccess"
            @vdropzone-queue-complete="onProccessDone"
        />
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone';

    export default {
        components: {
            vueDropzone: vue2Dropzone
        },
        props: {
            value: {
                type: [Array, String],
                default: () => []
            },
            maxSize: {
                type: Number,
                default: 2048
            },
            max: {
                type: Number,
                default: 10
            },
            token: {
                type: String,
                default: ""
            },
            uploading: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                files: this.value instanceof Array ? this.value : [],
                disableWatchValue: false,
                dropzoneOptions: {
                    url: sanjabUrl('helpers/dropzone/handler'),
                    thumbnailWidth: 150,
                    maxFilesize: this.maxSize/1024.0,
                    maxFiles: this.max,
                    timeout: 6000000,
                    addRemoveLinks: true,
                    paramName: "__sanjab_dropzone_js_files_" + this.token,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                    dictDefaultMessage: "فایل های خود را اینجا بندازید ",
                    dictFallbackMessage: "مرورگر شماامکان آپلود با درگ دراپ ندارد",
                    dictFileTooBig: "فایل خیلی سنگین است (@{{filesize}}MiB). حداکثر حجم مجاز: @{{maxFilesize}}MiB. ",
                    dictInvalidFileType: "این نوع از فایل را نمیتوانید آپلود کنید",
                    dictCancelUpload: "لغو آپلود",
                    dictCancelUploadConfirmation: "آیا برای لغو آپلود مطمئنید؟",
                    dictRemoveFile: "حذف فایل",
                    dictRemoveFileConfirmation: "برای حذف فایل مطمئن هستید؟",
                    dictMaxFilesExceeded: "شما فایل بیشتری نمیتوانید آپلود کنید",
                }
            };
        },
        methods: {
            onSuccess (file, response) {
                this.disableWatchValue = true;
                file.serverFile = response.filename;
                this.files.push(response.filename);
                this.$emit("input", this.files);
                setTimeout(() => this.disableWatchValue = false, 250);
            },
            onRemove (file, error, xhr) {
                this.disableWatchValue = true;
                if (this.files.indexOf(file.serverFile) >= 0) {
                    this.files.splice(this.files.indexOf(file.serverFile), 1);
                    this.$emit("input", this.files);
                }
                setTimeout(() => this.disableWatchValue = false, 250);
            },
            updateFiles (newValue) {
                this.files = [];
                this.$refs.dropzoneInstance.removeAllFiles();
                for (var i in newValue) {
                    if (typeof newValue[i] == "object" && Object.keys(newValue[i]).length > 0) {
                        this.files.push(newValue[i].name);
                        var file = { size: newValue[i].size, name: newValue[i].original, type: "image/jpeg", serverFile: newValue[i].name };
                        var url = newValue[i].url;
                        this.$refs.dropzoneInstance.manuallyAddFile(file, url);
                    } else if (typeof newValue[i] == 'string') {
                        this.files.push(newValue[i]);
                        var file = { size: '100', name: 'image', type: "image/jpeg", serverFile: newValue[i] };
                        this.$refs.dropzoneInstance.manuallyAddFile(file, '#');
                    }
                }
                this.disableWatchValue = true;
                this.$emit("input", this.files);
                setTimeout(() => this.disableWatchValue = false, 250);
                setTimeout(() => this.replaceTexts(), 250);
            },
            onProccess () {
                this.$emit("update:uploading", true);
            },
            onProccessDone () {
                this.$emit("update:uploading", false);
            },
            replaceTexts() {
                $(this.$el).find("span[data-dz-name]").each(function () {
                    if ($(this).text().match(/http(s?)\:\/\//) && !$(this).data('real-name')) {
                        $(this).data('real-name', $(this).text());
                        var typeFa = ($(this).text().match(/.*\.(jpg|jpeg|png|gif|bmp|svg)/) ? "تصویر" : "فایل");
                        $(this).html("<a href='" + $(this).text() + "' target='_blank'>" + typeFa + "</a>")
                    }
                });
            }
        },
        watch: {
            value (newValue, oldValue) {
                if (! this.disableWatchValue) {
                    if (typeof this.files instanceof Array && newValue instanceof Array) {
                        if (newValue.diff(this.files).length != 0) {
                            this.updateFiles(newValue);
                        }
                    } else {
                        this.updateFiles(newValue);
                    }
                }
            }
        },
    }
</script>

<style lang="scss">
    .dz-filename a {
        color: white;
        cursor: pointer !important;
    }
</style>
