<template>
  <div>
    <p class="text-sm text-gray-500">
      {{ trans('web::access_control.permissions.help') }}
    </p>
    <input
      v-model="query"
      type="text"
      :placeholder="trans('web::access_control.permissions.search')"
      class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
    <div class="mt-2 max-h-64 divide-y divide-gray-100 overflow-auto rounded-md border border-gray-200">
      <label
        v-for="permission in filtered"
        :key="permission"
        class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50"
      >
        <input
          v-model="model"
          type="checkbox"
          :value="permission"
          class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
        >
        <span class="text-gray-700">{{ permission }}</span>
      </label>
      <p
        v-if="! filtered.length"
        class="px-3 py-2 text-sm text-gray-400"
      >
        {{ trans('web::access_control.permissions.none') }}
      </p>
    </div>
    <p class="mt-1 text-xs text-gray-500">
      {{ trans('web::access_control.permissions.selected', { count: model.length }) }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useTranslations } from "@/composables/useTranslations";

// Array of permission name strings the group grants.
const model = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    availablePermissions: { type: Array, default: () => [] },
});

const { trans } = useTranslations();

const query = ref("");
const filtered = computed(() => {
    const needle = query.value.trim().toLowerCase();

    return needle
        ? props.availablePermissions.filter((permission) => permission.toLowerCase().includes(needle))
        : props.availablePermissions;
});
</script>
