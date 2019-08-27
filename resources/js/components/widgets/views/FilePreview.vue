<template>
    <b-row v-if="! hidden" class="sanjab-preview">
        <b-col v-for="(file, index) in mutableValue" :key="index" :cols="fileType(file.type) == 'image' || fileType(file.type) == 'other' ? 6 : 12" :sm="fileType(file.type) == 'image' || fileType(file.type) == 'other' ? 4 : 12" :md="fileType(file.type) == 'image' || fileType(file.type) == 'other' ? 4 : 12" :lg="fileType(file.type) == 'image' || fileType(file.type) == 'other' ? 2 : 12">
            <div v-if="fileType(file.type) == 'image'">
                <div class="d-flex">
                    <div class="position-relative">
                        <a :href="file.link ? file.link : file.value" target="_blank">
                            <b-img :src="file.preview" width="128" height="128" class="shadow my-2" thumbnail fluid />
                        </a>
                        <b-button v-if="! readonly" class="sanjab-preview-image-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                    </div>
                </div>
            </div>
            <div v-else-if="fileType(file.type) == 'audio'">
                <div class="d-flex">
                    <div class="position-relative w-100 d-flex align-items-center my-2">
                        <audio controls>
                            <source :src="file.preview" :type="file.type">
                            Your browser does not support the audio element.
                        </audio>
                        <b-button v-if="! readonly" class="sanjab-preview-audio-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>

                    </div>
                </div>
            </div>
            <div v-else-if="fileType(file.type) == 'video'">
                <div class="d-flex">
                    <div class="position-relative w-100 my-1">
                        <video class="rounded" width="100%" controls>
                            <source :src="file.preview" :type="file.type">
                            Your browser does not support the video element.
                        </video>
                        <b-button v-if="! readonly" class="sanjab-preview-video-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="d-flex">
                    <div class="position-relative w-100 my-1 other-file-type">
                        <b-button class="shadow" :href="file.link ? file.link : file.value" target="_blank" block><i class="material-icons mr-1">folder_open</i><b>{{ sanjabTrans('file') }}</b></b-button>
                        <b-button v-if="! readonly" class="sanjab-preview-image-remove-button" size="small" variant="danger" @click="removeFile(index)"><i class="material-icons">delete</i></b-button>
                    </div>
                </div>
            </div>
        </b-col>
    </b-row>
</template>

<script>
    export default {
        props: {
            value: {
                type: Array,
                default: () => []
            },
            readonly: {
                type: Boolean,
                default: true
            }
        },
        mounted () {
            this.mutableValue = this.value;
        },
        data() {
            return {
                mutableValue: [],
                hidden: false
            }
        },
        methods: {
            removeFile(index) {
                this.hidden = true;
                this.mutableValue.splice(index, 1);
                this.$emit("input", this.mutableValue);
                this.$emit("onRemove");
                this.hidden = false;
            },
            fileType(type) {
                if (type.match(/image\/.*/)) {
                    return 'image';
                } else if (type.match(/video\/.*/)) {
                    return 'video';
                } else if (type.match(/audio\/.*/)) {
                    return 'audio';
                }
                return 'other';
            }
        },
        watch: {
            value(newValue, oldValue) {
                this.hidden = true;
                this.mutableValue = newValue;
                this.hidden = false;
            }
        },
    }
</script>

<style lang="scss" scoped>

    .btn.sanjab-preview-remove-button {
        bottom: 20px;
    }

    .btn.sanjab-preview-video-remove-button {
        position: absolute;
        top: 0;
        left: 5px;
        width: 40px;
        height: 40px;
        padding: 4px;
        display: flex !important;
        justify-content: center;
        align-items: center;

        i {
            right: 1px;
        }
    }

    .btn.sanjab-preview-image-remove-button {
        position: absolute;
        width: 30px;
        height: 25px;
        padding: 6px;
        display: flex !important;
        justify-content: center;
        align-items: center;
        bottom: 1px !important;
        right: unset !important;
        transform: translateX(-50%);
        left: 50%;

        i {
            right: 1px;
        }
    }
    .other-file-type {
        position: relative;
        .btn.sanjab-preview-image-remove-button{
            bottom: 6px !important;
        }
        .shadow {
            height: 75px;
        }
    }

    .btn.sanjab-preview-audio-remove-button {
        position: relative;
        width: 30px;
        height: 30px;
        padding: 6px;
        margin-right: auto;
    }

    .sanjab-preview {
        audio {
            width: 92%;
        }
    }

    html[dir="rtl"] {
        .btn.sanjab-preview-image-remove-button {
            right: 38px;
        }
    }

    html[dir="ltr"] {
        .btn.sanjab-preview-image-remove-button {
            left: 38px;
        }
    }

</style>
