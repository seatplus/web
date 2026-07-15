<template>
  <span class="inline-flex items-center gap-x-1.5 rounded-full bg-indigo-50 py-0.5 pl-0.5 pr-1.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200">
    <EveImage
      :object="imageObject"
      :size="32"
      tailwind_class="h-5 w-5 rounded-full"
    />
    <span class="flex flex-col leading-tight">
      <span class="max-w-[12rem] truncate">{{ entity.name ?? entity.id }}</span>
      <span
        v-if="subText"
        class="max-w-[12rem] truncate text-[10px] text-indigo-400"
      >{{ subText }}</span>
    </span>
    <button
      type="button"
      class="ml-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:bg-indigo-500 focus:text-white focus:outline-hidden"
      @click="$emit('remove', entity.id)"
    >
      <span class="sr-only">Remove {{ entity.name ?? entity.id }}</span>
      <svg
        class="h-2 w-2"
        stroke="currentColor"
        fill="none"
        viewBox="0 0 8 8"
      >
        <path
          stroke-linecap="round"
          stroke-width="1.5"
          d="M1 1l6 6m0-6L1 7"
        />
      </svg>
    </button>
  </span>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue";
import { computed } from "vue";

const props = defineProps({
    // { id, name, category, ... } — a search result or a loaded selection.
    entity: { type: Object, required: true },
});

defineEmits(["remove"]);

// EveImage keys off `<category>_id`. Search results already carry it; loaded selections only have
// `{id, name, category}`, so derive it here to render the portrait/logo in both flows.
const imageObject = computed(() => {
    const category = props.entity?.category;

    if (! category) {
        return props.entity;
    }

    return { ...props.entity, [`${category}_id`]: props.entity.id };
});

// Corp/alliance context, shown only when the entity carries it.
const subText = computed(() => {
    const corporation = props.entity?.corporation?.name ?? props.entity?.corporation;
    const alliance = props.entity?.alliance?.name ?? props.entity?.alliance;

    return [corporation, alliance].filter((value) => typeof value === "string").join(" · ");
});
</script>
