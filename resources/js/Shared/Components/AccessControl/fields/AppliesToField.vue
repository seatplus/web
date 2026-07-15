<template>
  <div>
    <p class="text-sm text-gray-500">
      {{ trans('web::access_control.applies_to.help') }}
    </p>

    <!-- Mode: Only these / Everyone except -->
    <div class="mt-3 flex flex-wrap gap-2">
      <button
        v-for="m in ['only_these', 'everyone_except']"
        :key="m"
        type="button"
        class="rounded-md px-3 py-1.5 text-sm font-medium"
        :class="mode === m ? 'bg-indigo-600 text-white' : 'text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
        @click="mode = m"
      >
        {{ trans(`web::access_control.applies_to.mode.${m}`) }}
      </button>
    </div>

    <div class="mt-4">
      <EsiMultiselect
        v-model="included"
        :categories="['character', 'corporation', 'alliance']"
        :label="trans('web::access_control.applies_to.label')"
        :placeholder="trans('web::access_control.applies_to.label')"
      />
    </div>

    <!-- Never (forbidden) carve-outs -->
    <div class="mt-6 border-t border-gray-100 pt-6">
      <label class="block text-sm font-medium text-gray-700">
        {{ trans('web::access_control.applies_to.exclude') }}
      </label>
      <p class="text-sm text-gray-500">
        {{ trans('web::access_control.applies_to.exclude_help') }}
      </p>
      <div class="mt-2">
        <EsiMultiselect
          v-model="excluded"
          :categories="['character', 'corporation', 'alliance']"
          :label="trans('web::access_control.applies_to.exclude')"
          :placeholder="trans('web::access_control.applies_to.exclude')"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import { useTranslations } from "@/composables/useTranslations";

// only_these → ALLOWED, everyone_except → INVERSE (the parent maps to affiliation_type on submit).
const mode = defineModel("mode", { type: String, default: "only_these" });
const included = defineModel("included", { type: Array, default: () => [] });
const excluded = defineModel("excluded", { type: Array, default: () => [] });

const { trans } = useTranslations();
</script>
