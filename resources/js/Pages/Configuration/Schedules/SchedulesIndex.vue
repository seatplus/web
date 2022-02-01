<template>
  <Settings>
    <teleport to="#head">
      <title>{{ title('Schedules Settings') }}</title>
    </teleport>
    <ul class="divide-y divide-gray-200">
      <WideListElement
        v-for="schedule of schedules"
        :key="schedule.id"
        :url="$route('schedules.details', schedule.id)"
      >
        <template #avatar>
          <svg
            class="h-8 w-8 text-gray-400"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </template>
        <template #upper_left>
          {{ schedule.job }}.
        </template>
        <template #upper_right>
          {{ inversedExpressions[schedule.expression] }}
        </template>
        <template #navigation>
          <svg
            class="text-gray-400 h-5 w-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </WideListElement>
      <li>
        <inertia-link
          :href="$route('schedules.create')"
          class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out"
        >
          <div class="flex items-center px-4 py-4 sm:px-6">
            <div class="min-w-0 flex-1 flex items-center">
              <div class="flex overflow-x-visible">
                <svg
                  fill="none"
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                  class="w-8 h-8 text-gray-400"
                >
                  <path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="px-4 capitalize">
                create
              </div>
            </div>
            <div>
              <svg
                class="text-gray-400 h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </inertia-link>
      </li>
    </ul>
  </Settings>
</template>

<script>
import Settings from "../Settings"
import WideListElement from "@/Shared/WideListElement"
export default {
    name: "SchedulesIndex",
    components: {Settings, WideListElement},
    props: {
        schedules: {
            type: Array,
            required: true
        },
        expressions: {
            type: Object,
            required: true
        }
    },
    computed: {
        inversedExpressions() {
            return _.invert(this.expressions)
        }
    }
}
</script>

<style scoped>

</style>
