<template>
  <div>
    <AppHead app-title="Edit Schedule" />

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
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
                v-for="(cronExpression, description, index) in cron"
                :key="index"
                :value="cronExpression"
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
        <span class="flex-1 flex justify-between rounded-md  shadow-xs">
          <Link
            method="delete"
            as="button"
            :href="deleteUrl"
            class="text-right inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-hidden focus:border-red-700 focus:ring-indigo active:bg-red-700 transition duration-150 ease-in-out"
          >
            Delete
          </Link>

          <Link
            method="post"
            as="button"
            :data="{ expression, job }"
            preserve-state
            :href="storeUrl"
            class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-hidden focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
          >
            Save
          </Link>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from "vue";
import { Link } from '@inertiajs/vue3';
import InputGroup from "@/Shared/InputGroup.vue"
import SeatPlusSelect from "@/Shared/SeatPlusSelect.vue"
import AppHead from "@/Shared/AppHead.vue";
import SchedulesDelete from "@/actions/Seatplus/Web/Http/Controllers/Configuration/Schedules/SchedulesDelete";
import SchedulesPost from "@/actions/Seatplus/Web/Http/Controllers/Configuration/Schedules/SchedulesPost";

const props = defineProps({
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
});

const expression = ref('')
const job = ref(props.schedule.job)

const deleteUrl = computed(() => SchedulesDelete.url(props.schedule.id))

const storeUrl = computed(() => SchedulesPost.url())

onMounted(() => {
    nextTick(function () {
        expression.value = props.schedule.expression
    })
})
</script>

<style scoped>

</style>
