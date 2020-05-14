<template>
    <div></div>

</template>

<script>
export default {
    name: "Alerts",
    mounted() {
        const types = ['info', 'warning', 'error', 'success']

        this.$nextTick(function () {
            for (let type of types) {
                if (this.$page.flash[type])
                    this.$eventBus.$emit('notification', {
                        title: this.$I18n.trans(type),
                        text: this.$page.flash[type],
                        type: type,
                    })
            }
        })

    }
}
</script>
