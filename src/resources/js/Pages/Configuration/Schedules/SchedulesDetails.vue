<template>
    <Layout page="Edit Schedule" :active-sidebar-element="$route('server.settings')">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <InputGroup for="schedule" label="schedule">
                        <SeatPlusSelect v-model="expression" id="schedule">
                            <option v-for="(expression,description) in this.cron" :value="expression">{{ description }}</option>
                        </SeatPlusSelect>
                    </InputGroup>
                    <InputGroup for="job" label="Job">
                        {{ schedule.job }}
                    </InputGroup>

                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <span class="flex-1 flex justify-between rounded-md  shadow-sm">
                    <inertia-link method="delete" as="button" :href="$route('schedules.delete', schedule.id)" class="text-right inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring-indigo active:bg-red-700 transition duration-150 ease-in-out">
                            Delete
                      </inertia-link>

                      <inertia-link method="post" as="button" :data="this.$data" preserve-state :href="$route('schedules.updateOrCreate')" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
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
            schedule: {
                type: Object,
                required: true
            },
            cron: {
                type: Object,
                required: true
            },
        },
        data() {
            return {
                expression: '',
                job: this.schedule.job
            }
        },
        mounted() {
            this.$nextTick(function () {
                this.expression = this.schedule.expression
            })

        }
    }
</script>

<style scoped>

</style>
