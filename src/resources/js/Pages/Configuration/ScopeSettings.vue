<template>
    <Settings :layout-object="this.layoutObject">
        Scopes
    </Settings>

</template>

<script>
    import Settings from "./Settings"

    export default {
        name: "ScopeSettings",
        components: {Settings},
        data() {
            return {
                layoutObject: {
                    pageHeader: 'Server Settings',
                    pageDescription: 'Scope',
                    activeSidebarElement: route('server.settings')
                }
            }
        },
    }
</script>

<style scoped>

</style>
