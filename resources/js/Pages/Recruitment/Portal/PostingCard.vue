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
      <div
        v-if="applied"
        class="inline-flex items-center text-sm font-medium text-green-700"
      >
        <CheckCircleIcon class="h-5 w-5 mr-1.5" />
        Applied
      </div>

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
import { CheckCircleIcon } from "@heroicons/vue/24/solid";
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
  applied: {
    required: true,
    type: Boolean,
  },
  postApplicationUrl: {
    required: true,
    type: String,
  },
});

const isUserType = computed(() => props.posting.type === 'user');

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
