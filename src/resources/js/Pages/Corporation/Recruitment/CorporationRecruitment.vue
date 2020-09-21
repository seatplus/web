<template>



    <!-- Be sure to use this with a layout container that is full-width on mobile -->
    <div class="bg-white overflow-hidden shadow sm:rounded-lg my-5">
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
                <div class="ml-4 mt-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <EveImage tailwind_class="h-12 w-12 rounded-full" :size="256" :object="corporation" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ corporation.name }}
                            </h3>
                            <p class="text-sm leading-5 text-gray-500">
                                {{ corporation.alliance ? corporation.alliance.name : '' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div v-if="corporation.can_manage" class="ml-4 mt-4 flex-shrink-0 flex">
                    <span class="inline-flex rounded-md shadow-sm">
                        <button @click="remove(corporation)" type="button" class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700">
                            Delete Enlistment
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <!--<div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
                <div class="ml-4 mt-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">

                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">

                            </h3>
                            <p class="text-sm leading-5 text-gray-500">
                                <a href="#">

                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="ml-4 mt-2 flex-shrink-0" v-if="can_delete">
                  <span class="inline-flex rounded-md shadow-sm">
                    <button type="button" class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700">
                      Delete Enlistment
                    </button>
                  </span>
                </div>
            </div>
        </div>-->

        <div class="px-4 py-5 sm:p-6">

            <Applications></Applications>

        </div>

    </div>
</template>

<script>
import EveImage from "@/Shared/EveImage"
import Applications from "./Applications"
export default {
    name: "CorporationRecruitment",
    components: {Applications, EveImage},
    props: {
        corporation: {
            required: true
        },
    },
    methods: {
        remove(corporation) {
            this.$inertia.delete(this.$route('delete.corporation.recruitment', corporation.corporation_id))
        }
    }
}
</script>

<style scoped>

</style>
