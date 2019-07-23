<template>
    <div>
        <div v-if="contentOnly">
            <slot></slot>
        </div>
        <div v-else class="card">
            <div v-if="header" class="card-header card-header-sanjab">
                <h4 class="card-title">{{ title }}</h4>
                <p class="card-category">{{ description }}</p>
            </div>
            <div class="card-body">
                <slot></slot>
            </div>
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
            description: {
                type: String,
                default: ""
            },
            header: {
                type: Boolean,
                default: true
            },
            contentOnly: {
                type: Boolean,
                default: false
            }
        },
    }
</script>
