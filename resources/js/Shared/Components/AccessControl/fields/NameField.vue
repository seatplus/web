<template>
  <div>
    <label
      :for="id"
      class="block text-sm font-medium text-gray-700"
    >{{ trans('web::access_control.fields.name') }}</label>
    <input
      :id="id"
      v-model="model"
      type="text"
      class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
    <p
      v-if="error"
      class="mt-1 text-sm text-rose-600"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { useTranslations } from "@/composables/useTranslations";

const model = defineModel({ type: String, default: "" });

defineProps({
    error: { type: String, default: null },
    id: { type: String, default: "role-name" },
});

const { trans } = useTranslations();
</script>
