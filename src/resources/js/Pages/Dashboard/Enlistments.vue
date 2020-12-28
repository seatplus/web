<template>

    <div v-show="enlistments" class="pb-5 border-b border-gray-200 space-y-2">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Job Postings
        </h3>
        <p class="max-w-4xl text-sm leading-5 text-gray-500">Workcation is a property rentals website. Etiam ullamcorper massa viverra consequat, consectetur id nulla tempus. Fringilla egestas justo massa purus sagittis malesuada.</p>
        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <li v-for="enlistment in filteredEnlistments" class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                <div class="flex-1 flex flex-col p-8">
                    <!--<img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">-->
                    <EveImage tailwind_class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" :size="256" :object="enlistment.corporation" />
                    <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">{{ enlistment.corporation.name }}</h3>
                    <dl class="mt-1 flex-grow flex flex-col justify-between">
                        <dt class="sr-only">Alliance</dt>
                        <dd class="text-gray-500 text-sm leading-5">{{ enlistment.corporation.alliance ? enlistment.corporation.alliance.name : '' }}</dd>
                    </dl>
                </div>
                <div class="border-t border-gray-200">
                    <div class="-mt-px flex">
                        <!--<div class="w-0 flex-1 flex border-r border-gray-200">
                            &lt;!&ndash;Another element&ndash;&gt;
                        </div>-->
                        <div class="-ml-px w-0 flex-1 flex">
                            <inertia-link :href="$route('delete.user.application')" method="delete" v-if="activeApplication" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-red-700 font-medium border border-transparent rounded-bl-lg hover:text-red-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 text-red-700" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3">Remove Application</span>
                            </inertia-link>
                            <inertia-link v-else :href="$route('post.application')" method="post" :data="{corporation_id: enlistment.corporation_id}" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3">Apply</span>
                            </inertia-link>
                        </div>
                    </div>
                </div>
            </li>

        </ul>
    </div>

</template>

<script>
import Menu from "@/Shared/Menu"
import CharacterApplication from "./CharacterApplication"
import EveImage from "../../Shared/EveImage"
export default {
    name: "Enlistments",
    components: {EveImage, CharacterApplication, Menu},
    props: {
        enlistments: {
            type: Array,
            required: true
        },
        application: {
            required: true
        }
    },
    computed: {
        filteredEnlistments() {

            let application_id = this.application ? this.application.corporation_id : null;

            return application_id ? _.filter(this.enlistments, (enlistment) => _.isEqual(enlistment.corporation_id, application_id)) : this.enlistments
        },
        activeApplication() {
            return !_.isEmpty(this.application)
        }
    }
}
</script>

<style scoped>

</style>
