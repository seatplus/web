<template>
  <div
    v-if="entities.length"
    class="mt-2"
  >
    <p
      class="text-xs font-medium text-gray-400"
      :class="align === 'end' ? 'text-right' : ''"
    >
      {{ label }}
    </p>
    <div
      class="mt-1 flex flex-wrap gap-1.5"
      :class="align === 'end' ? 'justify-end' : ''"
    >
      <EntityChip
        v-for="entity in entities"
        :key="`${entity.entity_type ?? entity.category}-${entity.id}`"
        :entity="entity"
        :chip-class="chipClass"
      />
    </div>
  </div>
</template>

<script setup>
import EntityChip from "./EntityChip.vue";

defineProps({
    label: { type: String, required: true },
    // list of { id, entity_type | category, name }
    entities: { type: Array, default: () => [] },
    chipClass: { type: String, default: "bg-indigo-50 text-indigo-700" },
    align: { type: String, default: "start" },
});
</script>
