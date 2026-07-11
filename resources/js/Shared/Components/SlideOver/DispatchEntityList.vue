<template>
  <ul class="divide-y divide-gray-200">
    <DispatchableEntry
      v-for="(entity, index) in entities"
      :key="`${label} ${index}`"
      :entry="entity"
    />

    <li
      v-if="isLoading"
      class="px-6 py-4 text-center text-sm text-gray-400"
    >
      loading…
    </li>

    <li
      v-else-if="entities.length === 0"
      class="px-6 py-4 text-center text-sm text-gray-500"
    >
      {{ emptyText }}
    </li>

    <li
      v-if="nextUrl && !isLoading"
      class="px-6 py-3"
    >
      <button
        type="button"
        class="w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
        @click="load"
      >
        Load more
      </button>
    </li>
  </ul>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { post } from "@/Functions/http";
import { getEntities } from "@/actions/Seatplus/Web/Http/Controllers/Queue/DispatchJobController";
import DispatchableEntry from "./DispatchableEntry.vue";

const props = defineProps({
    params: {
        type: Object,
        required: true,
    },
    label: {
        type: String,
        default: "entry",
    },
    emptyText: {
        type: String,
        default: "no entries",
    },
});

const entities = ref([]);
const nextUrl = ref(getEntities.url());
const isLoading = ref(false);

// The endpoint returns a Laravel paginator; `links.next` carries the page cursor as
// a query string, so the same body params fetch each subsequent page.
const load = async () => {
    if (isLoading.value || nextUrl.value == null) {
        return;
    }

    isLoading.value = true;

    try {
        const page = await (await post(nextUrl.value, props.params)).json();

        entities.value.push(...page.data);
        nextUrl.value = page.links?.next ?? null;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(load);
</script>
