import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

let store = new Vuex.Store({
    state: {
        notificationItems: [],
        notificationEnabled: true,
    },
    mutations: {
        setNotificationItems (state, notificationItems) {
            state.notificationItems = notificationItems;
        },
        enableNotifications(state) {
            state.notificationEnabled = true;
        },
        disableNotifications(state) {
            state.notificationEnabled = false;
        },
        loadNotification(state, forceRefresh = false) {
            if (window.sanjab.notificationEventSourceEnabled) {
                if (forceRefresh && window.sanjab.notificationEventSource) {
                    window.sanjab.notificationEventSource.close();
                    window.sanjab.notificationEventSource = undefined;
                }
                if (window.sanjab.notificationEventSource === undefined) {
                    var self = this;
                    window.sanjab.notificationEventSource = new EventSource(sanjabUrl('/notifications/stream?force=' + forceRefresh + '&time=' + ((parseInt(Date.now() / 1000) - window.sanjab.serverTimeDiff) - 5)));
                    window.sanjab.notificationEventSource.addEventListener('message', function (event) {
                        let data = JSON.parse(event.data);
                        if (data.type == 'close') {
                            window.sanjab.notificationEventSource.close();
                            window.sanjab.notificationEventSource = undefined;
                            self.commit('loadNotification');
                        } else if (data.type == 'items') {
                            state.notificationItems = data.items;
                        }
                    }, false);
                }
            }
        },
        markAsRead() {
            var self = this;
            axios.get(sanjabUrl('notifications/mark-as-read'))
                .then(function (response) {
                    self.commit('loadNotification', true);
                })
                .catch(function (error) {});
        },
    }
});

store.watch((state) => state.notificationItems, function (newValue, oldValue) {
    let notificationItems = [];
    if (typeof Storage !== "undefined" && this.$sanjabStore.state.notificationEnabled) {
        for (let i in newValue) {
            for (let j in newValue[i].items) {
                let item = newValue[i].items[j];
                if (item.notificationSound === true || item.notificationToast === true) {
                    let notifiedBefore = null;
                    if (typeof item.id !== 'undefined') {
                        notifiedBefore = localStorage.getItem('sanjab_notification_' + item.id);
                        if (notifiedBefore == null) {
                            localStorage.setItem('sanjab_notification_' + item.id, parseInt(Date.now()/1000));
                        }
                    } else {
                        notifiedBefore = localStorage.getItem('sanjab_notification_' + JSON.stringify(item));
                        if (notifiedBefore == null) {
                            localStorage.setItem('sanjab_notification_' + JSON.stringify(item), parseInt(Date.now()/1000));
                        }
                    }
                    if (notifiedBefore == null) {
                        if (item.notificationSound === true) {
                            sanjabPlayNotificationSound();
                        }
                        if (item.notificationToast === true) {
                            notificationItems.push(item.title);
                        }
                    }
                }
            }
        }
    }
    notificationItems = notificationItems.slice(0, 3);
    for (let i in notificationItems) {
        setTimeout(() => sanjabToast(notificationItems[i], {icon: 'info', position: 'top'}), (i == notificationItems.length-1 ? 6000 : 3000) * i);
    }
});

export default store;
