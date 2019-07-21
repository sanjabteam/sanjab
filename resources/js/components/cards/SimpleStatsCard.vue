<template>
    <a :href="link">
        <div class="card card-stats">
            <div :class="'card-header card-header-icon card-header-' + variant">
                <div class="card-icon">
                    <i class="material-icons">{{ icon }}</i>
                </div>
                <p class="card-category">{{ title }}</p>
                <h3 v-if="data" class="card-title">{{ data.data }}</h3>
                <h3 v-else class="card-title">...</h3>
            </div>
            <div v-if="footerTitle" class="card-footer">
                <div class="stats">
                    <i v-if="footerIcon" class="material-icons">{{ footerIcon }}</i>
                    {{ footerTitle }}
                </div>
            </div>
        </div>
    </a>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: ""
            },
            icon: {
                type: String,
                default: "content_copy"
            },
            value: {
                type: [String, Number],
                default: "0"
            },
            link: {
                type: String,
                default: "javascript:void(0);"
            },
            footerTitle: {
                type: String,
                default: null
            },
            footerIcon: {
                type: String,
                default: null
            },
            variant: {
                type: String,
                default: "warning"
            },
            data: {
                type: [Object],
                default: null
            }
        },
    }
</script>
