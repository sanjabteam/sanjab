<template>
    <component :is="mobile ? 'div' : 'b-collapse'" :class="mobile ? '' : 'justify-content-end'" is-nav>
        <b-navbar-nav :class="mobile ? 'nav nav-mobile-menu ' : ''">
            <b-nav-item-dropdown v-for="(item, index) in menuItems" :key="index" right no-caret>
                <template v-slot:button-content>
                    <i class="material-icons">{{ item.icon }}</i>

                    <span v-if="item.badge" class="notification">{{ item.badge }}</span>
                    <p class="d-lg-none d-md-block">
                        {{ item.title }}
                    </p>
                </template>
                <template v-for="(dropdownItem, index2) in item.items">
                    <div v-if="dropdownItem == 0" :key="index + '_' + index2" class="dropdown-divider"></div>
                    <b-dropdown-item v-else :key="index + '_' + index2" :active="dropdownItem.active == true" :href="dropdownItem.link">{{ dropdownItem.title }}</b-dropdown-item>
                </template>
            </b-nav-item-dropdown>
        </b-navbar-nav>
    </component>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                default: () => []
            },
            mobile: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            menuItems() {
                return this.$sanjabStore.state.notificationItems ? this.$sanjabStore.state.notificationItems : [];
            }
        },
        mounted () {
            if (this.$sanjabStore.state.notificationItems.length == 0) {
                this.$sanjabStore.commit('setNotificationItems', this.items);
            }
            this.$sanjabStore.commit('loadNotification');
        },
    }
</script>
