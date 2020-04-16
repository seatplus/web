<template>
    <div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
        <div class="max-w-sm w-full ">
            <transition-group
                tag="div"
                :enter-active-class="notifications.length > 1 ? 'transform ease-out delay-300 duration-300 transition': 'transform ease-out duration-300 transition'"
                enter-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-500"
                leave-class="opacity-100"
                leave-to-class="opacity-0"
                move-class="transition ease-in-out duration-500"
            >
                <Notification
                    :key="notification.id"
                    :id="notification.id"
                    :payload="notification.payload"
                    v-for="(notification, index) in slicedNotifications"
                    :class="[{'mt-4': index > 0 }]"
                    @remove="removeNotification"
                />
            </transition-group>
        </div>
    </div>
</template>

<script>
    import Notification from "./Notification"
    import { v4 as uuidv4 } from 'uuid';

    export default {
        name: "Notifications",
        components: {Notification},
        data() {
            return {
                notifications: []
            }
        },
        computed: {
            slicedNotifications() {
                return this.notifications.slice(0,4)
            }
        },
        mounted() {
            this.$eventBus.$on('notification', payload => {
                const notification = {
                    id: this.getId(payload),
                    payload: payload
                }
                this.notifications.unshift(notification)
                setTimeout(() => {
                    this.hideNotification(notification.id)
                }, 5000)
            })
        },
        methods : {
            hideNotification(id) {
                this.notifications = _.reject(this.notifications, notification => {return notification.id === id})
            },
            getId(payload) {
                return payload.hasOwnProperty('id') ? payload.id : this.getUuid(payload)
            },
            getUuid(payload) {
                const notification =  this.storeInLocalStorage(payload)

                return notification.id
            },
            storeInLocalStorage(payload) {

                const notification = {
                    id: uuidv4(),
                    payload: payload
                }

                const notifications = JSON.parse(localStorage.getItem('notifications')) ?? []

                notifications.unshift(notification)

                localStorage.setItem('notifications', JSON.stringify(notifications))

                this.flipNotificationIndicator()

                return notification
            },
            removeNotification(id) {
                this.hideNotification(id)

                let notifications = JSON.parse(localStorage.getItem('notifications'))

                notifications = _.reject(notifications, notification => {return notification.id === id})

                localStorage.setItem('notifications' , JSON.stringify(notifications))

                return this.flipNotificationIndicator()
            },
            flipNotificationIndicator() {

                const notifications = JSON.parse(localStorage.getItem('notifications'))

                this.$eventBus.$emit('notification-indicator', notifications.length > 0)
            }
        }
    }

    /*
    * TODO: pagination next click prevention on no more
    * */
</script>


