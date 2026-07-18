<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 space-y-3">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <EveImage
          :object="{ corporation_id: application.corporation.corporation_id }"
          :size="64"
          tailwind_class="h-10 w-10 rounded"
        />
        <h3 class="text-base font-semibold text-gray-900">
          [{{ application.corporation.ticker }}] {{ application.corporation.name }}
        </h3>
      </div>
      <span
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
        :class="statusClass"
      >
        {{ statusLabel }}
      </span>
    </div>

    <div v-if="application.status === 'open' && application.total_stages > 0">
      <div class="flex items-center justify-between text-xs text-gray-500">
        <span>Stage {{ application.current_position + 1 }} of {{ application.total_stages }}: {{ application.current_stage }}</span>
        <span>{{ application.current_position }}/{{ application.total_stages }}</span>
      </div>
      <div class="mt-1 h-1.5 w-full rounded-full bg-gray-100">
        <div
          class="h-1.5 rounded-full bg-indigo-500"
          :style="{ width: progressPercent + '%' }"
        />
      </div>
    </div>

    <ol
      v-if="application.timeline.length > 0"
      class="space-y-1.5 border-t border-gray-100 pt-2"
    >
      <li
        v-for="(entry, index) in application.timeline"
        :key="index"
        class="flex items-center gap-2 text-sm"
      >
        <CheckCircleIcon
          v-if="entry.decision === 'accepted'"
          class="h-4 w-4 shrink-0 text-green-500"
        />
        <XCircleIcon
          v-else
          class="h-4 w-4 shrink-0 text-red-500"
        />
        <span class="text-gray-900">{{ entry.stage_label }}</span>
        <span class="text-gray-500">
          {{ entry.decision === 'accepted' ? 'passed' : 'rejected' }} by {{ entry.reviewer }}
        </span>
        <span class="ml-auto text-xs text-gray-400">{{ entry.at }}</span>
      </li>
    </ol>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { CheckCircleIcon, XCircleIcon } from "@heroicons/vue/24/solid";
import EveImage from "@/Shared/EveImage.vue";

const props = defineProps({
  application: {
    required: true,
    type: Object,
  },
});

const statusLabel = computed(() => ({
  open: "Under review",
  accepted: "Accepted",
  rejected: "Not selected",
}[props.application.status] ?? props.application.status));

const statusClass = computed(() => ({
  open: "bg-yellow-50 text-yellow-700",
  accepted: "bg-green-50 text-green-700",
  rejected: "bg-gray-100 text-gray-600",
}[props.application.status] ?? "bg-gray-100 text-gray-600"));

const progressPercent = computed(() => {
  if (!props.application.total_stages) {
    return 0;
  }

  return Math.min(100, Math.round((props.application.current_position / props.application.total_stages) * 100));
});
</script>
