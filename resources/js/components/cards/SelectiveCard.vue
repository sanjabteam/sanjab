<template>
    <div :class="{'inline-selective-card': inline}">
        <b-row>
            <b-col>
                <b class="selective-card-title">{{ title }}</b>
            </b-col>
            <b-col>
                <b-form-select v-model="currentCard" class="">
                    <option v-for="(card, index) in cards" :key="index" :value="index">{{ card.title }}</option>
                </b-form-select>
            </b-col>
        </b-row>
        <template v-if="currentCard !== null">
            <template v-for="(card, index) in cards">
                <div v-if="index == currentCard" :key="index" class="selective-card-container">
                    <b-row v-if="cardsData[index] != undefined">
                        <b-col>
                            <component :is="card.card.tag" :data="cardsData[index]" v-bind="card.card" />
                        </b-col>
                    </b-row>
                    <div v-else class="text-center text-danger my-2">
                        <b-spinner variant="default" class="align-middle">
                        </b-spinner>
                    </div>
                </div>
            </template>
        </template>
        <div v-else class="text-center text-danger my-2">
            <b-spinner variant="default" class="align-middle">
            </b-spinner>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: ""
            },
            cards: {
                type: Array,
                default: () => []
            },
            controller: {
                type: String,
                default: ""
            },
            controllerAction: {
                type: String,
                default: ""
            },
            controllerIndex: {
                type: Number,
                default: -1
            },
            controllerItem: {
                type: Number,
                default: 0
            },
            inline: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                currentCard: null,
                cardsData: []
            }
        },
        mounted () {
            var self = this;
            setTimeout(() => self.currentCard = 0, 100);
        },
        watch: {
            currentCard(newValue, oldValue) {
                var self = this;
                if (self.cardsData[newValue] === undefined) {
                    axios.post(sanjabUrl('helpers/selective-card/data'), {
                        controller: self.controller,
                        action: self.controllerAction,
                        index: self.controllerIndex,
                        item: self.controllerItem,
                        dataIndex: newValue
                    })
                    .then(function (response) {
                        self.cardsData[newValue] = response.data;
                        self.$forceUpdate();
                    }).catch(function (error) {
                        console.error(error);
                        sanjabHttpError(error.response.status);
                    });
                }
            }
        },
    }
</script>

<style lang="scss">
    .inline-selective-card {
        .bmd-form-group {
            position: absolute;
            padding-top: 5px;
            z-index: 5000;
        }

        .selective-card-title {
            position: absolute;
            z-index: 5000;
            padding-top: 5px;
        }

        .selective-card-container {
            padding-top: 10px;
        }
    }

    html[dir="ltr"] {
        .bmd-form-group {
            right: 25px;
        }

        .selective-card-title {
            left: 25px;
        }
    }

    html[dir="rtl"] {
        .bmd-form-group {
            left: 25px;
        }

        .selective-card-title {
            right: 25px;
        }
    }
</style>
