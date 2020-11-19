<template>
    <Layout page="Application" :active-sidebar-element="$route('corporation.recruitment')">
        <template v-slot:title>
            <PageHeader :breadcrumbs="[{name: 'Recruitment', route: 'corporation.recruitment'}]">
                User Application
                <template v-slot:primary>
                    <HeaderButton v-if="recruit.id" @click="impersonate" :secondary="true">
                        Impersonate
                    </HeaderButton>
                </template>
            </PageHeader>
        </template>


        <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <li class="col-span-2 bg-white rounded-lg shadow">

                Here shall be components of the most used review metrics such as wallet transactions

                <!--<div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                        &lt;!&ndash; Content goes here &ndash;&gt;
                        header
                        &lt;!&ndash; We use less vertical padding on card headers on desktop than on body sections &ndash;&gt;
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        &lt;!&ndash; Content goes here &ndash;&gt;
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                        &lt;!&ndash; Content goes here &ndash;&gt;
                        header
                        &lt;!&ndash; We use less vertical padding on card headers on desktop than on body sections &ndash;&gt;
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        &lt;!&ndash; Content goes here &ndash;&gt;
                    </div>
                </div>-->

            </li>

            <li class="col-span-1 bg-white rounded-lg shadow">
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                            <!-- Heroicon name: check -->
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <div class="mt-3 sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Application of {{ recruit.main_character.name }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                                This will decide if one or all of the following characters are allowed to join the corporation: {{ characters }}
                            </p>
                            <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                                Remember to invite them in game as well.
                            </p>
                            <!--Decision-->
                            <div class="mt-6 sm:mt-5 sm:border-t sm:border-gray-200 sm:pt-5">
                                <div role="group" aria-labelledby="label-notifications">
                                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                                        <div>
                                            <div class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700" id="label-notifications">
                                                Decision
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <div class="max-w-lg">
                                                <p class="text-sm leading-5 text-gray-500">Decide if the recruit should be accepted to corporation or not.</p>
                                                <div class="mt-4">
                                                    <div class="flex items-center">
                                                        <input id="accept_application" v-model="form.decision" value="accepted" name="accept_application" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                        <label for="accept_application" class="ml-3">
                                                            <span class="block text-sm leading-5 font-medium text-gray-700">Accept application</span>
                                                        </label>
                                                    </div>
                                                    <div class="mt-4 flex items-center">
                                                        <input id="reject_application" v-model="form.decision" value="rejected" name="reject_application" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                        <label for="reject_application" class="ml-3">
                                                            <span class="block text-sm leading-5 font-medium text-gray-700">Reject application</span>
                                                        </label>
                                                    </div>
                                                    <!--<div class="mt-4 flex items-center">
                                                        <input id="push_nothing" name="push_notifications" type="radio" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                                        <label for="push_nothing" class="ml-3">
                                                            <span class="block text-sm leading-5 font-medium text-gray-700">No push notifications</span>
                                                        </label>
                                                    </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Explanation-->
                            <div v-if="form.decision === 'rejected' " class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                                <label for="explanation" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                                    Explanation
                                </label>
                                <div class="mt-1 sm:mt-0 sm:col-span-2">
                                    <div class="max-w-lg flex rounded-md shadow-sm">
                                        <textarea id="explanation" v-model="form.explanation" rows="3" class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5" required></textarea>
                                    </div>
                                    <p v-if="$page.props.errors.explanation" class="mt-2 text-sm text-red-600"> {{ $page.errors.explanation[0] }}</p>
                                    <p class="mt-2 text-sm text-gray-500">Write a few sentences about the decision, in that recruiters in the future might learn from past decisions.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <span class="flex w-full rounded-md shadow-sm">
                          <button @click="submit"  type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Submit review
                          </button>
                        </span>
                    </div>
                </div>
            </li>
        </ul>



    </Layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import PageHeader from "@/Shared/Layout/PageHeader";
import HeaderButton from "@/Shared/Layout/HeaderButton";
export default {
    name: "UserApplication",
    components: {HeaderButton, PageHeader, Layout},
    props: {
        recruit: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: {
                decision: null,
                explanation: null
            }
        }
    },
    computed: {
        characters() {
            return _.map(this.recruit.characters, (character) => character.name ).join(', ')
        }
    },
    methods: {
        impersonate() {
            return this.$inertia.visit(this.$route('impersonate.recruit', this.recruit.id))
        },
        submit() {

            if(this.recruit.id)
                return this.$inertia.post(this.$route('review.user.application', this.recruit.id), this.form);

            this.$inertia.post(this.$route('review.character.application', this.recruit.main_character.character_id), this.form);
        }
    }
}
</script>

<style scoped>

</style>
