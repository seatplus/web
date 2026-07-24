<template>
  <div>
    <!-- Mobile: compact indicator (the full stepper is too tall on small screens). -->
    <div class="md:hidden">
      <div class="flex items-baseline justify-between">
        <p class="text-sm font-medium text-indigo-600">
          {{ steps[current]?.label }}
        </p>
        <p class="text-xs text-gray-500">
          {{ current + 1 }} / {{ steps.length }}
        </p>
      </div>
      <div class="mt-2 overflow-hidden rounded-full bg-gray-200">
        <div
          class="h-1.5 rounded-full bg-indigo-600 transition-all duration-300 ease-out"
          :style="{ width: `${pct}%` }"
        />
      </div>
    </div>

    <!-- md+ : full bordered stepper -->
    <nav
      aria-label="Progress"
      class="hidden md:block"
    >
      <ol
        role="list"
        class="rounded-md border border-gray-300 md:flex md:divide-y-0"
      >
        <li
          v-for="(step, stepIdx) in steps"
          :key="step.key"
          class="relative md:flex md:flex-1"
        >
          <!-- Complete -->
          <span
            v-if="statusOf(stepIdx) === 'complete'"
            class="flex w-full items-center"
          >
            <span class="flex items-center px-6 py-4 text-sm font-medium">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-600">
                <CheckIcon
                  class="size-6 text-white"
                  aria-hidden="true"
                />
              </span>
              <span class="ml-4 text-sm font-medium text-gray-900">{{ step.label }}</span>
            </span>
          </span>

          <!-- Current -->
          <span
            v-else-if="statusOf(stepIdx) === 'current'"
            class="flex items-center px-6 py-4 text-sm font-medium"
            aria-current="step"
          >
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-indigo-600">
              <span class="text-indigo-600">{{ stepNumber(stepIdx) }}</span>
            </span>
            <span class="ml-4 text-sm font-medium text-indigo-600">{{ step.label }}</span>
          </span>

          <!-- Upcoming -->
          <span
            v-else
            class="flex items-center"
          >
            <span class="flex items-center px-6 py-4 text-sm font-medium">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-gray-300">
                <span class="text-gray-500">{{ stepNumber(stepIdx) }}</span>
              </span>
              <span class="ml-4 text-sm font-medium text-gray-500">{{ step.label }}</span>
            </span>
          </span>

          <!-- Arrow separator for md screens and up -->
          <template v-if="stepIdx !== steps.length - 1">
            <div
              class="absolute top-0 right-0 hidden h-full w-5 md:block"
              aria-hidden="true"
            >
              <svg
                class="size-full text-gray-300"
                viewBox="0 0 22 80"
                fill="none"
                preserveAspectRatio="none"
              >
                <path
                  d="M0 -2L20 40L0 82"
                  vector-effect="non-scaling-stroke"
                  stroke="currentcolor"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </template>
        </li>
      </ol>
    </nav>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { CheckIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    // [{ key, label }]
    steps: { type: Array, required: true },
    current: { type: Number, default: 0 },
});

const pct = computed(() => (props.steps.length ? ((props.current + 1) / props.steps.length) * 100 : 0));

const statusOf = (index) => {
    if (index < props.current) {
        return "complete";
    }

    if (index === props.current) {
        return "current";
    }

    return "upcoming";
};

const stepNumber = (index) => String(index + 1).padStart(2, "0");
</script>
