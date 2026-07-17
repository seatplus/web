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
    {{ label }}
  </span>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue";
import { computed, ref, watchEffect } from "vue";
import { getJson } from "@/Functions/http";
import { getEntityFromId } from "@/actions/Seatplus/Web/Http/Controllers/Shared/HelperController";

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

// Affiliations can reference an entity the app has never persisted (e.g. a character/corp added by
// id via ESI search), so the resource carries no name. Resolve it lazily by id through the cached
// resolve.id endpoint — the portrait already renders from the id alone.
const resolvedName = ref(null);

watchEffect(async () => {
    if (props.entity?.name || ! props.entity?.id) {
        return;
    }

    const data = await getJson(getEntityFromId.url(props.entity.id));
    resolvedName.value = data?.name ?? null;
});

const label = computed(() => props.entity?.name ?? resolvedName.value ?? props.entity?.id);
</script>
