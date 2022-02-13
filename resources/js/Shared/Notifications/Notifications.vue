<template>
  <div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
    <div class="max-w-sm w-full ">
      <transition-group
        tag="div"
        :enter-active-class="notifications.length > 1 ? 'transform ease-out delay-300 duration-300 transition': 'transform ease-out duration-300 transition'"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-500"
        leave-from-class="opacity-100"
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
            return _.uniqBy(this.notifications.slice(0,4), 'id')
        },
        flash() {
            return this.$page.props.flash
        }
    },
    watch: {
        flash(newValue) {
            const types = ['info', 'warning', 'error', 'success']

            this.$nextTick(function () {
                for (let type of types) {
                    if (newValue[type]) {
                        const payload = {
                            title: this.$I18n.trans(type),
                            text: newValue[type],
                            type: type,
                        }

                        const notification = {
                            id: uuidv4(),
                            payload: payload
                        }

                        this.notifications.unshift(notification)

                        setTimeout(() => {
                            this.hideNotification(notification.id)
                        }, 10000)
                    }
                }


            })
        }
    },
    methods : {
        hideNotification(id) {
            this.notifications = _.reject(this.notifications, notification => {return notification.id === id})
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
        }
    }
}
</script>


