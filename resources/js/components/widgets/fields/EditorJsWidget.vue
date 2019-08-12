<template>
    <editor
        ref="editor"
        :holder-id="'codex-editor-' + randomInt"
        :init-data.sync="initialValue"
        @save="onSave"
        @change="onChange"
        :image="imageConfig"
        :link="linkConfig"
        :embed="{}"
        :header="{}"
        :list="{}"
        :code="{}"
        :inlineCode="{}"
        :table="{}"
        :raw="{}"
        :delimiter="{}"
        :quote="{}"
        :warning="{}"
        :paragraph="{}"
        :checklist="{}"
    />
</template>

<script>
    import { Editor } from 'vue-editor-js';

    export default {
        components: {
            Editor,
        },
        props: {
            value: {
                type: [Object, String],
                default: {}
            },
        },
        mounted () {
            this.initialzed = true;
        },
        data() {
            return {
                initialzed: false,
                randomInt: parseInt(Math.random() * 100000000),
                timer: null,
                initialValue: {},
                mutableValue: {},
                imageConfig: {
                    config: {
                        endpoints: {
                            byFile: sanjabUrl('helpers/editor-js/image-upload'),
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }
                },
                linkConfig: {
                    config: {
                        endpoint: sanjabUrl('helpers/editor-js/link'),
                    }
                }
            }
        },
        methods: {
            onChange() {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => this.$refs.editor.save(), 750);
            },
            onSave(val) {
                this.$emit('input', val);
                this.mutableValue = val;
            }
        },
        watch: {
            value(newValue, oldValue) {
                if (this.initialzed && newValue instanceof Object && JSON.stringify(newValue) != JSON.stringify(this.mutableValue)) {
                    if (this.$refs.editor.editor) {
                        if (typeof delete this.$refs.editor.editor.destroy === 'function') {
                            this.$refs.editor.editor.destroy();
                            this.$refs.editor.editor = null;
                        }
                        delete this.$refs.editor.editor;
                    }
                    this.initialValue = newValue;
                }
            }
        },
    }
</script>

<style lang="scss">
.codex-editor__redactor {
    padding-bottom: 30px !important;
}
html[dir="rtl"] {
    .ce-toolbar .ce-toolbox {
        left: 0;
    }
}
</style>
