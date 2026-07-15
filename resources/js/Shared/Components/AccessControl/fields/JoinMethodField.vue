<template>
  <div class="grid gap-3 sm:grid-cols-2">
    <button
      v-for="method in joinMethods"
      :key="method.key"
      type="button"
      class="rounded-lg border p-4 text-left transition"
      :class="model === method.key
        ? 'border-indigo-600 ring-1 ring-indigo-600'
        : 'border-gray-200 hover:border-gray-300'"
      @click="model = method.key"
    >
      <span class="block text-sm font-medium text-gray-900">{{ method.label }}</span>
      <span class="mt-1 block text-xs text-gray-500">{{ method.description }}</span>
    </button>
  </div>
</template>

<script setup>
const model = defineModel({ type: String, default: "manual" });

defineProps({
    // RoleTypeMetadata::all() — [{ key, label, description, uses_eligibility, ... }]
    joinMethods: { type: Array, default: () => [] },
});
</script>
