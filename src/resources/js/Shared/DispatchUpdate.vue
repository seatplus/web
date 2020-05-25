<template>

    <div class="flex-1 bg-white flex flex-col justify-between">
        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-3 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="pl-3 py-3 border-b border-gray-200 bg-gray-50"></th>
                            <th class="pl-3 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-3 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                            <template v-for="(character, index) of characters">
                                <ReadyRow :character="character" :job_name="job_name" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"/>
                                <PendingRow :character="character" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"/>
                                <CompletedRow :character="character" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"/>
                                <FailedRow :character="character" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"/>
                            </template>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




</template>

<script>
    import EveImage from "./EveImage"
    import Timer from "./Timer"
    import CompletedRow from "./DispatchUpdates/CompletedRow"
    import ReadyRow from "./DispatchUpdates/ReadyRow"
    import FailedRow from "./DispatchUpdates/FailedRow"
    import PendingRow from "./DispatchUpdates/PendingRow"

    export default {
        name: "DispatchUpdate",
        components: {PendingRow, FailedRow, ReadyRow, CompletedRow, Timer, EveImage},
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
    }
</script>

<style scoped>

</style>
