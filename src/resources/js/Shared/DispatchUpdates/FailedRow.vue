<template>
    <tr v-if="isFailed()">
        <td class="pl-3 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
            {{character.name}}
        </td>
        <td class="pl-3 py-4 text-sm leading-5">
            <div class="text-red-400">
                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </td>
        <td class="pl-3 py-4 text-sm leading-5 text-gray-500">
            <Timer :created="character.job.failed_at" />
        </td>
        <td class="px-3 py-4 text-sm leading-5"></td>
    </tr>
</template>

<script>
    import Timer from "../Timer"
    export default {
        name: "FailedRow",
        components: {Timer},
        props: ['character'],
        methods: {
            isFailed() {

                let job = this.character.job

                return _.isNull(job)? false : _.isEqual(job.status, 'failed')
            },
        }
    }
</script>

<style scoped>

</style>
