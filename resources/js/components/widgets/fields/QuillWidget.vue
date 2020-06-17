<template>
    <quill-editor v-model="mutableValue"
                :options="editorOptions"
                ref="editor" />
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: ""
            },
        },
        mounted () {
            if (document.dir == "rtl" && (this.value == null || this.value.length == 0) && (this.mutableValue == null || this.mutableValue.length == 0)) {
                this.$refs.editor.quill.format('direction', 'rtl');
                this.$refs.editor.quill.format('align', 'right');
            }
            setTimeout(() => this.loaded = true, 250);
        },
        data() {
            return {
                loaded: false,
                mutableValue: "",
                editorOptions: {
                    placeholder: sanjabTrans('insert_text_here'),
                    modules: {
                        imageUpload: {
                            url: sanjabUrl('helpers/quill/image-upload'),
                            name: 'file',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            callbackKO: serverError => {
                                sanjabHttpError(serverError.code);
                            },
                        },
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'header': 1 }, { 'header': 2 }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'script': 'sub' }, { 'script': 'super' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                            [{ 'direction': 'rtl' }],
                            [{ 'size': ['small', false, 'large', 'huge'] }],
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'font': [] }],
                            [{ 'align': [] }],
                            ['clean'],
                            ['link', 'image', 'video']
                        ]
                    }
                }
            }
        },
        watch: {
            mutableValue(newValue, oldValue) {
                if (this.loaded) {
                    this.$emit('input', newValue);
                }
            },
            value(newValue, oldValue) {
                setTimeout(() => this.mutableValue = this.value, 250);
            }
        },
    }
</script>

<style lang="scss">
    html[dir="rtl"] {
        .ql-snow .ql-picker:not(.ql-color-picker):not(.ql-icon-picker) svg {
            right: -14px !important;
        }

        .ql-editor.ql-blank:before {
            direction: rtl;
            text-align: right;
        }
    }
</style>
