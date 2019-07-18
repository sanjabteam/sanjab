<template>
    <div>
        <span v-for="(option, index) in showOptions" :key="index" class="btn btn-warning">{{ option }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: () => {}
            },
            widget: {
                type: Object,
                default: () => {}
            }
        },
        computed: {
            showOptions(newValue, oldValue) {
                var out = [];
                if (this.data[this.widget.name] instanceof Array) {
                    for (var i in this.widget.options) {
                        if (this.data[this.widget.name].includes(this.widget.options[i].value)) {
                            out.push(this.widget.options[i].label);
                        }
                    }
                }
                return out;
            }
        },
    }
</script>
