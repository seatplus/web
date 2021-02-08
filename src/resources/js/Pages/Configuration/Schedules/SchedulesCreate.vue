<template>
    <Layout page="Create Schedule" :active-sidebar-element="$route('server.settings')">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <InputGroup for="schedule" label="schedule">
                        <SeatPlusSelect v-model="expression" id="schedule">
                            <option v-for="(expression,description) in this.cron" :value="expression">{{ description }}</option>
                        </SeatPlusSelect>
                    </InputGroup>
                    <InputGroup for="job" label="Job">
                        <SeatPlusSelect v-model="job" id="job">
                            <option v-for="(job) in this.jobs" :value="job">{{ job }}</option>
                        </SeatPlusSelect>
                    </InputGroup>

                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <span class="inline-flex rounded-md shadow-sm">
                      <inertia-link method="post" :data="this.$data" preserve-state :href="$route('schedules.updateOrCreate')" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                            Save
                      </inertia-link>
                </span>
            </div>
        </div>
    </Layout>
</template>

<script>
    import Layout from "@/Shared/Layout"
    import InputGroup from "@/Shared/InputGroup"
    import SeatPlusSelect from "@/Shared/SeatPlusSelect"
    export default {
        name: "SchedulesCreate",
        components: {SeatPlusSelect, InputGroup, Layout},
        props: {
            cron: {
                type: Object,
                required: true
            },
            jobs: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                expression: '',
                job: ''
            }
        }
    }
</script>

<style scoped>

</style>
