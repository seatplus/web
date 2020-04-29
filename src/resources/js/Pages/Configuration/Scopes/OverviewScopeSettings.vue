<template>
    <Settings :layout-object="this.layoutObject">
        <ul>
            <WideListElement
                v-for="(entry,index) in this.entries"
                :key="entry.selectedEntity.id"
                :url="$route('edit.scopes.settings', entry.selectedEntity.id)"
                :class="{'border-t border-gray-200': index >0}"
            >
                <template v-slot:avatar>
                    <eve-image :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'" :object="entry.selectedEntity" :size="128"/>
                </template>
                <template v-slot:upper_left>{{entry.selectedEntity.name}}</template>
                <template slot="lower_right">

                </template>
                <template slot="navigation">
                    <svg class="text-gray-400 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
            </WideListElement>
            <li>
                <inertia-link :href="$route('view.create.scopes')" :class="['block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out', {'border-t border-gray-200': this.entries.length > 0 }]">
                    <div class="flex items-center px-4 py-4 sm:px-6">
                        <div class="min-w-0 flex-1 flex items-center">
                            <div class="flex overflow-x-visible">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8"><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="px-4 capitalize">
                                create
                            </div>
                        </div>
                        <div>
                            <svg class="text-gray-400 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </inertia-link>
            </li>
        </ul>
    </Settings>

</template>

<script>
    import Settings from "@/Pages/Configuration/Settings"
    import WideListElement from "@/Shared/WideListElement"
    import EveImage from "@/Shared/EveImage"

    export default {
        name: "OverviewScopeSettings",
        components: {WideListElement, EveImage, Settings},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entries: {
                type: Array,
                required: true
            },
        },
        data() {
            return {
                layoutObject: {
                    pageHeader: 'Server Settings',
                    pageDescription: 'Scope',
                    activeSidebarElement: route('server.settings')
                },
            }
        },
        methods: {

        },
        computed: {

        },
    }
</script>

<style scoped>

</style>
