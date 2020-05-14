import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

let store = new Vuex.Store({
    state: {
        notificationItems: [],
        notificationEnabled: true,
        notificationsEventCreated: false
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
                var self = this;
                // Allow only one event source per user.
                if (localStorage.sanjabNotificationTabId && localStorage.sanjabNotificationTabId != sanjabBrowserTabId && localStorage.sanjabNotificationLastTimeEventSource && parseInt(localStorage.sanjabNotificationLastTimeEventSource) + 610 > parseInt(Date.now()/1000)) {
                    sanjabBroadcastChannel.addEventListener('message', function (event) {
                        if (typeof event.data == 'object' && event.data.type == 'change_notification_event_source' && event.data.tab == sanjabBrowserTabId) {
                            self.commit('loadNotification');
                        }
                        if (typeof event.data == 'object' && event.data.type == 'set_notifications') {
                            state.notificationItems = event.data.items;
                        }
                    });
                } else if (window.sanjab.notificationEventSource === undefined) {
                    window.sanjab.notificationEventSource = new EventSource(sanjabUrl('/notifications/stream?force=' + forceRefresh + '&time=' + ((parseInt(Date.now() / 1000) - window.sanjab.serverTimeDiff) - 5)));
                    window.sanjab.notificationEventSource.addEventListener('message', function (event) {
                        let data = JSON.parse(event.data);
                        if (data.type == 'close') {
                            window.sanjab.notificationEventSource.close();
                            window.sanjab.notificationEventSource = undefined;
                            self.commit('loadNotification');
                        } else if (data.type == 'items') {
                            state.notificationItems = data.items;
                            sanjabBroadcastChannel.postMessage({type: 'set_notifications', items: data.items});
                        }
                    }, false);

                    localStorage.sanjabNotificationLastTimeEventSource = parseInt(Date.now()/1000);
                    if (! state.notificationsEventCreated) {
                        window.addEventListener('beforeunload', function () {
                            localStorage.removeItem('sanjabNotificationTabId');
                            if (sanjabBrowserTabs.length > 0) {
                                sanjabBroadcastChannel.postMessage({type: 'change_notification_event_source', tab: sanjabBrowserTabs.filter((tabId) => tabId != sanjabBrowserTabId)[0]});
                            }
                        });
                        sanjabBroadcastChannel.addEventListener('message', function (event) {
                            if (typeof event.data == 'object' && event.data.type == 'mark_notifications_as_read') {
                                self.commit('markAsRead');
                            }
                        });
                        localStorage.sanjabNotificationTabId = sanjabBrowserTabId;
                        state.notificationsEventCreated = true;
                    }
                }
            }
        },
        markAsRead() {
            var self = this;
            if (window.sanjab.notificationEventSource === undefined) {
                sanjabBroadcastChannel.postMessage({type: 'mark_notifications_as_read'});
            } else {
                axios.get(sanjabUrl('notifications/mark-as-read'))
                    .then(function (response) {
                        self.commit('loadNotification', true);
                    })
                    .catch(function (error) {});
            }
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
                        notifiedBefore = localStorage.getItem('sanjabNotification_' + item.id);
                        if (notifiedBefore == null) {
                            localStorage.setItem('sanjabNotification_' + item.id, parseInt(Date.now()/1000));
                        }
                    } else {
                        notifiedBefore = localStorage.getItem('sanjabNotification_' + JSON.stringify(item));
                        if (notifiedBefore == null) {
                            localStorage.setItem('sanjabNotification_' + JSON.stringify(item), parseInt(Date.now()/1000));
                        }
                    }
                    if (notifiedBefore == null) {
                        if (item.notificationSound === true && window.sanjab.notificationEventSource) {
                            sanjabPlayNotificationSound();
                        }
                        if (item.notificationToast === true) {
                            notificationItems.push(item);
                        }
                    }
                }
            }
        }
    }
    notificationItems = notificationItems.slice(0, 3);
    for (let i in notificationItems) {
        setTimeout(() => sanjabToast(notificationItems[i].title, {icon: 'info', position: 'top', html: notificationItems[i].link ? '<a class="btn btn-primary" href="' + notificationItems[i].link + '">' + sanjabTrans('show') + '</a>' : null}), (i == notificationItems.length-1 ? 6000 : 3000) * i);
    }
});

export default store;
