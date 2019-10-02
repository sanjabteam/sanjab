<template>
    <div>
        <v-money v-model="mutableValue" class="form-control" v-bind="money" />
    </div>
</template>

<script>
  import {Money} from 'v-money';

    export default {
        props: {
            value: {
                type: [Number, String],
                default: 0,
            },
            decimal: {
                type: String,
                default: '.'
            },
            thousands: {
                type: String,
                default: ','
            },
            prefix: {
                type: String,
                default: ''
            },
            postfix: {
                type: String,
                default: ' $'
            },
            precision: {
                type: Number,
                default: 2
            }
        },
        mounted () {
            if (this.value instanceof Number || this.value instanceof String) {
                this.mutableValue = this.value;
            }
        },
        data () {
            return {
                mutableValue: 0,
                money: {
                    decimal: this.decimal,
                    thousands: this.thousands,
                    prefix: this.prefix,
                    suffix: this.postfix,
                    precision: this.precision,
                    masked: false
                }
            }
        },
        watch: {
            mutableValue(newValue, oldValue) {
                this.$emit('input', newValue);
            },
            value(newValue, oldValue) {
                if (typeof newValue === 'number' || typeof newValue === 'string') {
                    this.mutableValue = newValue;
                }
            }
        },
        components: {'v-money': Money},
    }
</script>

