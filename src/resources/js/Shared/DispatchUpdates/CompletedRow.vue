<template>
    <tr v-if="isComplete()">
        <td class="pl-3 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
            {{character.name}}
        </td>
        <td class="pl-3 py-4 text-sm leading-5">
            <div class="text-green-400">
                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </td>
        <td class="pl-3 py-4 text-sm leading-5 text-gray-500">
            <Timer :created="character.job.completed_at" />
        </td>
        <td class="px-3 py-4 text-sm leading-5"></td>
    </tr>
</template>

<script>
    import Timer from "../Timer"
    export default {
        name: "CompletedRow",
        components: {Timer},
        props: ['character'],
        computed: {
            completed() {
                return this.isComplete() ? this.character.job.id : null
            }
        },
        methods: {
            isComplete() {

                let job = this.character.job

                return _.isNull(job)? false : _.isEqual(job.status, 'completed')
            },
        }
    }
</script>

<style scoped>

</style>
