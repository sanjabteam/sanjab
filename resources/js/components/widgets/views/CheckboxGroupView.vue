<template>
    <div>
        <span v-for="(selected, index) in selectedOptions" :key="index">{{ selected.value ? '✅' : '❌' }}{{ selected.name }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            widget: {
                type: Object,
                default: () => {return {};}
            },
            data: {
                type: Object,
                default: () => {return {};}
            }
        },
        computed: {
            selectedOptions() {
                var out = [];
                if (this.data[this.widget.name] instanceof Array) {
                    for (var i in this.widget.options) {
                        if (this.data[this.widget.name].includes(this.widget.options[i].value)) {
                            out.push({name: this.widget.options[i].text, value: true});
                        } else {
                            out.push({name: this.widget.options[i].text, value: false});
                        }
                    }
                }
                return out;
            }
        },
    }
</script>
