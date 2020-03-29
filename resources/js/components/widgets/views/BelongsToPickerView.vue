<template>
    <div>
        <span v-for="(option, index) in showOptions" :key="index" :class="crudType == 'show' ? 'btn btn-warning': ''">{{ option }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: () => {return {};}
            },
            widget: {
                type: Object,
                default: () => {return {};}
            },
            crudType: {
                type: String,
                default: 'index'
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
                    if (out.length == 0 && typeof this.data[this.widget.name] == 'string') {
                        out.concat(this.data[this.widget.name]);
                    }
                } else {
                    for (var i in this.widget.options) {
                        if (this.data[this.widget.name] == this.widget.options[i].value) {
                            out.push(this.widget.options[i].label);
                        }
                    }
                    if (out.length == 0 && typeof this.data[this.widget.name] == 'string') {
                        out.push(this.data[this.widget.name]);
                    }
                }
                return out;
            }
        },
    }
</script>
