<template>
    <nav class="flex-1 px-2 py-4 bg-gray-800">

        <div v-for="(category, name, index) in sidebarEntries">
            <h3 :class="['text-xs leading-4 font-semibold text-white uppercase tracking-wider',{'mt-3' : index > 0}]">
                {{category.name}}
            </h3>
            <inertia-link v-for="(entry,index) in category.entries" :key="entry.name" :href="route(entry.route)" :class="[{'mt-1': index > 0, 'text-white bg-gray-900': isActive(entry.route), 'text-gray-300 hover:text-white hover:bg-gray-700 focus:text-white': !isActive(entry.route)},'group flex items-center px-2 py-2 text-sm leading-5 font-medium rounded-md focus:outline-none focus:bg-gray-700 transition ease-in-out duration-150']">
                <svg class="mr-3 h-6 w-6 text-gray-300 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150" stroke="currentColor" fill="none" :viewBox="entry.viewbox" v-html="entry.content"></svg>
                {{entry.name}}
            </inertia-link>
        </div>

    </nav>
</template>

<script>
    export default {
        name: "StaticSidebarDesktop",
        props: {
            main_character: Object,
            activeEntryUrl: {
                type: String,
                required: true,
            }
        },
        data() {
            return {
                sidebarEntries: this.$page.sidebar
                /*[
                    {
                        name: 'Character',
                        entries: [
                            {name: 'Dashboard', route: 'character.assets', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>'},
                            {name: 'Team', route:'home', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'},
                            {name: 'Projects', route: 'character.assets', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>'},
                            {name: 'Calendar', route: 'character.assets', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'},
                            {name: 'Documents', route: 'character.assets', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>'},
                            {name: 'Reports', route: 'character.assets', viewbox: '0 0 24 24', content: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'}
                        ]
                    }
                ]*/
            }
        },
        methods: {
            isActive(routename) {

                console.log(route(routename).url(), this.activeEntryUrl)

                return this.activeEntryUrl === route(routename).url()
            }
        },
    }
</script>

<style scoped>

</style>
