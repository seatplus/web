<template>
    <ul class="divide-y divide-gray-200 overflow-y-auto">
        <li @click="dispatchJob(entity)" v-for="(entity, index) of entities" :class="['px-6 py-5 relative', {'cursor-pointer': entity.batch === 'ready'}]">
            <div class="group flex justify-between items-center space-x-2">
                <div class="-m-1 p-1 block">
                    <span class="absolute inset-0 group-hover:bg-gray-50"></span>
                    <div class="flex-1 flex items-center min-w-0 relative">
                        <EveImage :object="entity" :size="256" tailwind_class="h-10 w-10 rounded-full" />
                        <div class="ml-4 truncate">
                            <div class="text-sm leading-5 font-medium text-gray-900 truncate">{{ entity.name }}</div>
                            <div class="text-sm leading-5 text-gray-500 truncate">
                                <span v-if="entity.batch === 'ready'">job can be dispatched</span>
                                <span v-if="entity.batch.status === 'pending'"><Time :timestamp="entity.batch.time" /> </span>
                                <span v-if="entity.batch.state === 'finished'"><Time :timestamp="entity.batch.time" /></span>
                                <span v-if="entity.batch.state === 'failures'"><Time :timestamp="entity.batch.time" /></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <svg v-if="entity.batch === 'ready'" class="h-8 w-8 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="entity.batch.status === 'pending'" class="h-8 w-8 text-yellow-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="entity.batch.state === 'finished'" class="h-8 w-8 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-if="entity.batch.state === 'failures'" class="h-8 w-8 text-red-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
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
    props: ['dispatch_transfer_object'],
    data() {
        return {
            job_name: this.dispatch_transfer_object.manual_job,
            entities: []
        }
    },
    methods: {
        dispatchJob(entity) {

            if(entity.batch !== 'ready')
                return

            axios.post(this.$route('dispatch.job', {
                character_id: entity.character_id,
                corporation_id: entity.corporation_id,
            }), {
                dispatch_transfer_object: this.dispatch_transfer_object
            })

            setTimeout(() => this.getEntities(), 100)

        },
        async getEntities() {
            axios.post(this.$route('manual_job.entities'), this.dispatch_transfer_object).then((response) => this.entities = response.data)
        }
    },
    created() {
        this.getEntities();
    }
}
</script>

<style scoped>

</style>
