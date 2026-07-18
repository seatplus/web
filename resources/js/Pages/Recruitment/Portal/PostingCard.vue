<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col space-y-3">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <EveImage
          :object="{ corporation_id: posting.corporation.corporation_id }"
          :size="64"
          tailwind_class="h-10 w-10 rounded"
        />
        <div>
          <h3 class="text-base font-semibold text-gray-900">
            [{{ posting.corporation.ticker }}] {{ posting.corporation.name }}
          </h3>
          <p
            v-if="posting.corporation.alliance"
            class="text-sm text-gray-500"
          >
            {{ posting.corporation.alliance.name }}
          </p>
        </div>
      </div>
      <span
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
        :class="isUserType ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700'"
      >
        {{ isUserType ? 'Whole account' : 'Single character' }}
      </span>
    </div>

    <div v-if="posting.stages.length > 0">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
        Review process
      </p>
      <ol class="mt-1 flex flex-wrap gap-1.5">
        <li
          v-for="stage in posting.stages"
          :key="stage.position"
          class="inline-flex items-center rounded bg-gray-50 px-2 py-0.5 text-xs text-gray-600"
        >
          {{ stage.position + 1 }}. {{ stage.label }}
        </li>
      </ol>
    </div>

    <div class="mt-auto pt-2">
      <!-- Applied: show this account's progress + reviewer-action timeline for this corporation. -->
      <template v-if="application">
        <div class="flex items-center justify-between">
          <span
            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
            :class="statusClass"
          >
            {{ statusLabel }}
          </span>
          <span
            v-if="application.status === 'open' && application.total_stages > 0"
            class="text-xs text-gray-500"
          >
            Stage {{ application.current_position + 1 }} of {{ application.total_stages }}: {{ application.current_stage }}
          </span>
        </div>

        <div
          v-if="application.status === 'open' && application.total_stages > 0"
          class="mt-1 h-1.5 w-full rounded-full bg-gray-100"
        >
          <div
            class="h-1.5 rounded-full bg-indigo-500"
            :style="{ width: progressPercent + '%' }"
          />
        </div>

        <ol
          v-if="application.timeline.length > 0"
          class="mt-2 space-y-1 border-t border-gray-100 pt-2"
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
      </template>

      <!-- Not applied: apply form. -->
      <form
        v-else
        class="space-y-2"
        @submit.prevent="submit"
      >
        <select
          v-if="!isUserType"
          v-model="form.character_id"
          required
          class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option
            :value="null"
            disabled
          >
            Select a character&hellip;
          </option>
          <option
            v-for="character in characters"
            :key="character.character_id"
            :value="character.character_id"
          >
            {{ character.name }}
          </option>
        </select>

        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
        >
          {{ form.processing ? 'Applying&hellip;' : 'Apply' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { CheckCircleIcon, XCircleIcon } from "@heroicons/vue/24/solid";
import { computed } from "vue";
import EveImage from "@/Shared/EveImage.vue";

const props = defineProps({
  posting: {
    required: true,
    type: Object,
  },
  characters: {
    required: true,
    type: Array,
  },
  application: {
    required: false,
    type: Object,
    default: null,
  },
  postApplicationUrl: {
    required: true,
    type: String,
  },
});

const isUserType = computed(() => props.posting.type === 'user');

const statusLabel = computed(() => ({
  open: "Under review",
  accepted: "Accepted",
  rejected: "Not selected",
}[props.application?.status] ?? props.application?.status));

const statusClass = computed(() => ({
  open: "bg-yellow-50 text-yellow-700",
  accepted: "bg-green-50 text-green-700",
  rejected: "bg-gray-100 text-gray-600",
}[props.application?.status] ?? "bg-gray-100 text-gray-600"));

const progressPercent = computed(() => {
  if (!props.application?.total_stages) {
    return 0;
  }

  return Math.min(100, Math.round((props.application.current_position / props.application.total_stages) * 100));
});

const form = useForm({
  corporation_id: props.posting.corporation_id,
  character_id: null,
});

function submit() {
  form.post(props.postApplicationUrl, {
    preserveScroll: true,
  });
}
</script>
