<template>
    <tr v-if="isReady()">
        <td class="pl-3 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
            {{character.name}}
        </td>
        <td class="pl-3 py-4 text-sm leading-5">
            <div class="text-gray-400">
                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </td>
        <td class="pl-3 py-4 text-sm leading-5 text-gray-500">
            job can be dispatched
        </td>
        <td class="px-3 py-4 text-sm leading-5">
            <span class="inline-flex rounded-md shadow-sm">
                <inertia-link :href="$route('dispatch.job')" method="post"
                              :data="{character_id: character.character_id, job: job_name}"
                              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
                >
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884zM18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" clip-rule="evenodd"/>
                    </svg>
                    Update
                </inertia-link>
            </span>

        </td>
    </tr>
</template>

<script>
    import Timer from "../Timer"
    export default {
        name: "ReadyRow",
        components: {Timer},
        props: ['character', 'job_name'],
        methods: {
            isReady() {

                let job = this.character.job

                return _.isNull(job)
            },
        }
    }
</script>

<style scoped>

</style>
