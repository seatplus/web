<template>
    <ul class="divide-y divide-gray-200 overflow-y-auto">
        <li @click="dispatchJob(character)" v-for="(character, index) of characters" :class="['px-6 py-5 relative', {'cursor-pointer': isReady(character)}]">
            <div class="group flex justify-between items-center space-x-2">
                <div class="-m-1 p-1 block">
                    <span class="absolute inset-0 group-hover:bg-gray-50"></span>
                    <div class="flex-1 flex items-center min-w-0 relative">
                        <EveImage :object="character" :size="256" tailwind_class="h-10 w-10 rounded-full" />
                        <div class="ml-4 truncate">
                            <div class="text-sm leading-5 font-medium text-gray-900 truncate">{{ character.name}}</div>
                            <div class="text-sm leading-5 text-gray-500 truncate">
                                <span v-if="isReady(character)">job can be dispatched</span>
                                <span v-if="isPending(character)"><Time :timestamp="getIsoTimestamp()" /> </span>
                                <span v-if="isComplete(character)"><Time :timestamp="getIsoTimestamp(character.job.completed_at)" /></span>
                                <span v-if="isFailed(character)"><Time :timestamp="getIsoTimestamp(character.job.failed_at)" /></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <svg v-if="isReady(character)" class="h-8 w-8 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="isPending(character)" class="h-8 w-8 text-yellow-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="isComplete(character)" class="h-8 w-8 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="isFailed(character)" class="h-8 w-8 text-red-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </li>
    </ul>




</template>

<script>
import EveImage from "./EveImage"
import Time from "./Time"
import dayjs from 'dayjs'

export default {
    name: "DispatchUpdate",
    components: {Time, EveImage},
    props: ['dispatchable_jobs'],
    data() {
        return {
            job_name: this.dispatchable_jobs.job_name
        }
    },
    computed: {
        characters() {
            return this.dispatchable_jobs.characters
        }
    },
    methods: {
        dispatchJob(character) {

            /*:href="$route('dispatch.job')" method="post"
                                :data="{character_id: character.character_id, job: job_name}*/
            if(this.isReady(character))
                return this.$inertia.post(this.$route('dispatch.job'), {character_id: character.character_id, job: this.job_name})
        },
        isReady(character) {

            let job = character.job

            return _.isNull(job)
        },
        isPending(character) {

            let job = character.job

            let bool =  _.isNull(job)? false : _.isEqual(job.status, 'pending') || _.isEqual(job.status, 'reserved')

            if(bool)
                this.$inertia.reload({
                    method: 'get',
                    data: {},
                    preserveScroll: true,
                    only: ['dispatchable_jobs'],
                })

            return bool
        },
        isComplete(character) {

            let job = character.job

            return _.isNull(job)? false : _.isEqual(job.status, 'completed')
        },
        isFailed(character) {

            let job = character.job

            return _.isNull(job)? false : _.isEqual(job.status, 'failed')
        },
        getIsoTimestamp(timestamp = false) {

            return timestamp ? dayjs.unix(_.toNumber(timestamp)).toISOString() : dayjs().toISOString()
        }
    }
}
</script>

<style scoped>

</style>
