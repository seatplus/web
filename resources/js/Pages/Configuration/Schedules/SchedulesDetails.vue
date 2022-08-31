<template>
  <div>
    <teleport to="#head">
      <title>{{ title('Edit Schedule') }}</title>
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
                v-for="(expression, description, index) in cron"
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
            {{ schedule.job }}
          </InputGroup>
        </div>
      </div>
      <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <span class="flex-1 flex justify-between rounded-md  shadow-sm">
          <Link
            method="delete"
            as="button"
            :href="route('schedules.delete', schedule.id)"
            class="text-right inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring-indigo active:bg-red-700 transition duration-150 ease-in-out"
          >
            Delete
          </Link>

          <Link
            method="post"
            as="button"
            :data="$data"
            preserve-state
            :href="route('schedules.updateOrCreate')"
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
import InputGroup from "@/Shared/InputGroup.vue"
import SeatPlusSelect from "@/Shared/SeatPlusSelect.vue"
import { Link } from '@inertiajs/inertia-vue3'
export default {
    name: "SchedulesDetails",
    components: {SeatPlusSelect, InputGroup, Link},
    props: {
        schedule: {
            type: Object,
            required: true
        },
        cron: {
            type: Object,
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
