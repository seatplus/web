<template>
    <div class="divide-y-2">
        <div v-for="application in this.user_applications" class="bg-white overflow-hidden shadow rounded-lg">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <!-- Content goes here -->
                <!-- We use less vertical padding on card headers on desktop than on body sections -->

                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="ml-4 mt-2">
                        <div class="flex items-center">
                            <div>
                                <EveImage tailwind_class="h-12 w-12 rounded-full" :size="256" :object="application.main_character" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg leading-6 font-medium text-indigo-600">
                                    {{ application.main_character.name }}
                                </h3>
                                <!--<p class="text-xs leading-4 font-medium text-gray-500 group-hover:text-gray-700 group-focus:underline transition ease-in-out duration-150">
                                    View profile
                                </p>-->
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 mt-2 flex-shrink-0 flex">
                            <!--<span class="inline-flex rounded-md shadow-sm">
                                <button @click="impersonateUser(application.user)" type="button" class="relative inline-flex items-center px-4 py-2 border border-grey-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                                    Impersonate
                                </button>
                            </span>-->
                        <span class="ml-3 inline-flex rounded-md shadow-sm">
                                <inertia-link :href="$route('user.application', application.user.id)" type="button" class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition ease-in-out duration-150" >
                                    Handle Application
                                </inertia-link>
                            </span>
                    </div>
                </div>

            </div>
            <ul class="divide-y divide-gray-200">
                <!--Characters-->
                <Applicant v-for="applicant in application.characters" :key="applicant.character_id" :character="applicant" />
            </ul>
        </div>
        <!--<UserApplicationModal v-model="modal_application" />-->

    </div>
</template>

<script>
import Applicant from "./Applicant";
import EveImage from "@/Shared/EveImage";
import Modal from "@/Shared/Modals/Modal";

export default {
    name: "UserApplications",
    components: {Modal, EveImage, Applicant},
    props: {
        applications: {
            required: true,
            type: Array
        }
    },
    computed: {
        user_applications() {
            return _.filter(this.applications, (application) => application.is_user)
        }
    },
    methods: {
        impersonateUser(user) {
            let url = this.$route('impersonate.recruit', user.id)

            return this.$inertia.visit(url);
        },
        handleApplication(application) {
            this.modal_application = application
        }
    },
    data() {
        return {
            modal_application: null
        }
    }
}
</script>

<style scoped>

</style>
