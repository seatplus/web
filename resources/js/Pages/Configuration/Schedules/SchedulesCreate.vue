<template>
  <div>
    <teleport to="#head">
      <title>{{ title('Create Schedule') }}</title>
    </teleport>

    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <InputGroup
            for="schedule"
            label="schedule"
          >
            <SeatPlusSelect
              id="schedule"
              v-model="expression"
            >
              <option
                v-for="(expression,description, index) in cron"
                :key="index"
                :value="expression"
              >
                {{ description }}
              </option>
            </SeatPlusSelect>
          </InputGroup>
          <InputGroup
            for="job"
            label="Job"
          >
            <SeatPlusSelect
              id="job"
              v-model="job"
            >
              <option
                v-for="(job, index) in jobs"
                :key="index"
                :value="job"
              >
                {{ job }}
              </option>
            </SeatPlusSelect>
          </InputGroup>
        </div>
      </div>
      <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <span class="inline-flex rounded-md shadow-sm">
          <Link
            method="post"
            :data="$data"
            preserve-state
            :href="$route('schedules.updateOrCreate')"
            class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
          >
            Save
          </Link>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
    import InputGroup from "@/Shared/InputGroup"
    import SeatPlusSelect from "@/Shared/SeatPlusSelect"
    import { Link } from '@inertiajs/inertia-vue3'
    export default {
        name: "SchedulesCreate",
        components: {SeatPlusSelect, InputGroup, Link},
        props: {
            cron: {
                type: Object,
                required: true
            },
            jobs: {
                type: Array,
                required: true
            },
            activeSidebarElement: {
                type: String,
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
