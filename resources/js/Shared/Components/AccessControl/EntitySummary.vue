<template>
  <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
    <h3 class="text-sm font-semibold text-gray-900">
      {{ title }}
    </h3>
    <p
      v-if="help"
      class="mt-1 text-sm text-gray-500"
    >
      {{ help }}
    </p>

    <div class="mt-3 flex flex-wrap gap-2">
      <template v-if="entities.length">
        <EntityChip
          v-for="entity in entities"
          :key="`${entity.entity_type}-${entity.id}`"
          :entity="entity"
          chip-class="bg-indigo-50 text-indigo-700"
        />
      </template>
      <span
        v-else
        class="text-sm text-gray-400"
      >
        {{ empty }}
      </span>
    </div>

    <!-- Optional "never / excluded" carve-outs -->
    <div
      v-if="excluded.length"
      class="mt-3 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3"
    >
      <span class="text-xs font-medium text-gray-500">{{ excludedLabel }}</span>
      <EntityChip
        v-for="entity in excluded"
        :key="`ex-${entity.entity_type}-${entity.id}`"
        :entity="entity"
        chip-class="bg-rose-50 text-rose-700"
      />
    </div>
  </div>
</template>

<script setup>
import EntityChip from "./EntityChip.vue";
import { useTranslations } from "@/composables/useTranslations";

defineProps({
    title: { type: String, required: true },
    help: { type: String, default: null },
    // list of { id, entity_type, name }
    entities: { type: Array, default: () => [] },
    excluded: { type: Array, default: () => [] },
    empty: { type: String, default: "" },
});

const { trans } = useTranslations();
const excludedLabel = trans("web::access_control.applies_to.exclude");
</script>
