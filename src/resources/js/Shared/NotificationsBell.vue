<template>
    <button class="p-1 text-gray-400 rounded-full hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:shadow-outline focus:text-gray-500">
        <!--<svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>-->

        <span class="inline-block relative">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span v-if="status" class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full text-white shadow-solid bg-red-400"></span>
        </span>

    </button>
</template>

<script>
    export default {
        name   : "NotificationsBell",
        data() {
            return {
                status: this.hasNotifications
            }
        },
        mounted() {
            this.$eventBus.$on('notification-indicator', status => {
                this.status = status
            })
        },
        computed: {

            hasNotifications() {
                const notifications = JSON.parse(localStorage.getItem('notifications'))

                return !_.isEmpty(notifications)
            }
        }
    }
</script>

<style scoped>

</style>
