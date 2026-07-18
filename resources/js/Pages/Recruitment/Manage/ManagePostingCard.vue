<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 space-y-4">
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
        :class="posting.type === 'user' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700'"
      >
        {{ posting.type === 'user' ? 'Whole account' : 'Single character' }}
      </span>
    </div>

    <div class="space-y-2">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
        Review stages
      </p>
      <p class="text-xs text-gray-500">
        Applications advance through these stages in order. The dropdown sets <span class="font-medium">who reviews</span> each stage:
        <span class="font-medium">Any recruiter</span> lets anyone allowed to accept or deny applications for this corporation review it,
        or pick a control group to restrict that stage to its members (e.g. stage&nbsp;1 &ldquo;Junior HR&rdquo;, stage&nbsp;2 &ldquo;Senior HR&rdquo;).
      </p>

      <div
        v-for="(stage, index) in stagesForm.stages"
        :key="index"
        class="flex items-center gap-2"
      >
        <span class="text-sm text-gray-400 w-5 text-right">{{ index + 1 }}.</span>
        <input
          v-model="stage.label"
          type="text"
          placeholder="Stage name (e.g. Interview)"
          class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
        <select
          v-model="stage.role_id"
          aria-label="Reviewed by"
          title="Who can review this stage"
          class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option :value="null">
            Any recruiter (no group)
          </option>
          <option
            v-for="group in controlGroups"
            :key="group.id"
            :value="group.id"
          >
            {{ group.name }}
          </option>
        </select>
        <button
          v-if="stagesForm.stages.length > 1"
          type="button"
          class="text-gray-400 hover:text-red-600"
          title="Remove stage"
          @click="removeStage(index)"
        >
          <XMarkIcon class="h-5 w-5" />
        </button>
        <span
          v-else
          class="h-5 w-5"
          aria-hidden="true"
        />
      </div>

      <p
        v-if="stagesForm.stages.length === 1"
        class="text-xs text-gray-400 pl-7"
      >
        A posting keeps at least one stage.
      </p>

      <div
        v-if="stagesForm.errors.stages"
        class="text-sm text-red-600"
      >
        {{ stagesForm.errors.stages }}
      </div>

      <button
        type="button"
        class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
        @click="addStage"
      >
        + Add stage
      </button>
    </div>

    <WatchlistSection
      :watched="posting.watched"
      :watchlist-url="posting.watchlist_url"
    />

    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
      <button
        type="button"
        :disabled="closeForm.processing"
        class="text-sm font-medium text-red-600 hover:text-red-500 disabled:opacity-50"
        @click="close"
      >
        Close posting
      </button>
      <button
        type="button"
        :disabled="stagesForm.processing"
        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
        @click="saveStages"
      >
        Save stages
      </button>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import EveImage from "@/Shared/EveImage.vue";
import WatchlistSection from "./WatchlistSection.vue";

const props = defineProps({
  posting: {
    required: true,
    type: Object,
  },
  controlGroups: {
    required: true,
    type: Array,
  },
});

const stagesForm = useForm({
  stages: props.posting.stages.map((stage) => ({
    label: stage.label,
    role_id: stage.role_id,
  })),
});

const closeForm = useForm({});

function addStage() {
  stagesForm.stages.push({ label: "", role_id: null });
}

function removeStage(index) {
  stagesForm.stages.splice(index, 1);
}

function saveStages() {
  stagesForm.put(props.posting.stages_url, {
    preserveScroll: true,
  });
}

function close() {
  closeForm.delete(props.posting.close_url, {
    preserveScroll: true,
  });
}
</script>
