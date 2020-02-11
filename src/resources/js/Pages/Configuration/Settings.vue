<template>
    <Layout>
        Settings
        <Commands />
        <b-card title="Server Settings" no-body>
            <b-card-header header-tag="nav">
                <b-nav card-header tabs >
                    <b-nav-item v-for="entry in this.navItems" :key="entry.id" :active="isActive(entry.route)" >
                        <inertia-link :href="route(entry.route)" preserve-state>
                            {{entry.name}}
                        </inertia-link>
                    </b-nav-item>
                </b-nav>
            </b-card-header>
            <b-card-body>
                <slot />
            </b-card-body>
        </b-card>


        <!--<UserList :users="users"></UserList>-->
    </Layout>
</template>

<script>
    import Layout from "@/Shared/Layout"
    import Commands from "@/Pages/Configuration/Commands"

    export default {
        name: "Settings",
        components: {Layout, Commands},
        data() {
            return {
                navItems: [
                    {id: 1, route: 'server.settings', name: 'User List'},
                    {id: 2, route: 'settings.scopes', name: 'Scope Settings'}
                ]
            }
        },
        methods: {
            isActive(string) {
                return route().current(string);
            },
        }
    }
</script>

<style scoped>

    .card-header {
        background-color: rgba(0,0,0,.03)
    }

    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        background-color: #fff;
        border-color:
            #dee2e6
            #dee2e6
            #fff;
    }
</style>
