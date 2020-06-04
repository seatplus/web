<template>
    <tr v-if="isPending()">
        <td class="pl-3 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
            {{character.name}}
        </td>
        <td class="pl-3 py-4 text-sm leading-5">
            <div class="text-yellow-400">
                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </td>
        <td class="pl-3 py-4 text-sm leading-5 text-gray-500">
            <Timer :created="this.timer" />
        </td>
        <td class="px-3 py-4 text-sm leading-5"></td>
    </tr>
</template>

<script>
    import Timer from "../Timer"
    import dayjs from 'dayjs'

    export default {
        name: "PendingRow",
        components: {Timer},
        props: ['character'],
        data() {
            return {
                timer: null,
                interval: null,
            }
        },
        created() {
            this.reloadData();
            this.interval =  setInterval(this.reloadData, 3000)
        },
        destroyed() {
            clearInterval(this.interval)
        },
        mounted() {
            this.timer = dayjs().unix()
        },
        methods: {
            isPending() {

                let job = this.character.job

                return _.isNull(job)? false : _.isEqual(job.status, 'pending') || _.isEqual(job.status, 'reserved')
            },
            reloadData() {

                if(!this.isPending())
                    return

                this.$inertia.reload({
                    method: 'get',
                    data: {},
                    preserveScroll: true,
                    only: ['dispatchable_jobs'],
                })
            }
        }
    }
</script>

<style scoped>

</style>
