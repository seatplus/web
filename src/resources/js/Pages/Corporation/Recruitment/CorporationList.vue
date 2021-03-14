<template>
    <ul class="grid grid-cols-1 gap-6 px-4 sm:px-6">
        <li v-for="corporation in corporations" :key="corporation.corporation_id" class="col-span-1 bg-white rounded-lg shadow">
            <div class="w-full flex items-center justify-between p-6 space-x-6">
                <div class="flex-1 truncate">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-gray-900 text-sm leading-5 font-medium truncate">{{ corporation.name }}</h3>
                        <span v-if="corporation.alliance" class="flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">{{ corporation.alliance }}</span>
                    </div>
                    <p class="mt-1 text-gray-500 text-sm leading-5 truncate">How should scopes be applied?</p>
                </div>
                <EveImage :object="corporation" :size="256" tailwind_class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" />
            </div>
            <div class="border-t border-gray-200">
                <div class="-mt-px flex">
                    <div class="w-0 flex-1 flex border-r border-gray-200">
                        <button @click="create(corporation, 'character')" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3">Recruits only</span>
                        </button>
                    </div>
                    <div class="-ml-px w-0 flex-1 flex">
                        <button @click="create(corporation, 'user')"  class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                            </svg>
                            <span class="ml-3">All Characters</span>
                        </button>
                    </div>
                </div>
            </div>
        </li>


<!--        <InfiniteLoading @infinite="infiniteHandler" spinner="waveDots" force-use-infinite-wrapper=".main.flex-1">
            <div slot="no-more"></div>
        </InfiniteLoading>-->
    </ul>
</template>

<script>
//TODO: Infinite Loading
//import InfiniteLoading from "vue-infinite-loading"
import EveImage from "@/Shared/EveImage"
export default {
    name: "CorporationList",
    components: {EveImage,
        //InfiniteLoading
    },
    data() {
        return {
            corporations: [],
            page: 1
        }
    },
    methods: {
        infiniteHandler($state) {
            axios.get(this.$route('get.affiliated.corporations', 'can open or close corporations for recruitment'), {
                params: {
                    page: this.page,
                },
            }).then(({ data }) => {
                if (data.data.length) {
                    this.page += 1;
                    this.corporations.push(...data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
        create(corporation, type) {

            this.$inertia.post(this.$route('create.corporation.recruitment'), {corporation_id: corporation.corporation_id, type: type})
        }
    }
}
</script>

<style scoped>

</style>
