<template>
  <span
    class="inline-flex items-center gap-x-1 rounded-md py-0.5 pl-0.5 pr-1.5 text-xs font-medium"
    :class="chipClass"
  >
    <EveImage
      :object="imageObject"
      :size="32"
      tailwind_class="h-4 w-4 rounded-full"
    />
    {{ entity.name ?? entity.id }}
  </span>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue";
import { computed } from "vue";

const props = defineProps({
    // { id, name, category | entity_type }
    entity: { type: Object, required: true },
    chipClass: { type: String, default: "bg-indigo-50 text-indigo-700" },
});

// EveImage keys off `<category>_id`. Form selections carry `category`; resource entities carry
// `entity_type` — support both so the portrait/logo renders in every flow.
const imageObject = computed(() => {
    const category = props.entity?.category ?? props.entity?.entity_type;

    if (! category) {
        return props.entity;
    }

    return { ...props.entity, [`${category}_id`]: props.entity.id };
});
</script>
