<template>
  <div>
    <label class="block text-sm font-medium text-gray-700">
      {{ trans('web::access_control.eligibility.label') }}
    </label>
    <p class="text-sm text-gray-500">
      {{ trans('web::access_control.eligibility.help') }}
    </p>
    <div class="mt-2">
      <EsiMultiselect
        v-model="model"
        :categories="['corporation', 'alliance']"
        :label="trans('web::access_control.eligibility.label')"
        :placeholder="trans('web::access_control.eligibility.label')"
      />
    </div>
  </div>
</template>

<script setup>
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import { useTranslations } from "@/composables/useTranslations";

// Array of { id, name, category } — corp/alliance criteria entities.
const model = defineModel({ type: Array, default: () => [] });

const { trans } = useTranslations();
</script>
