<template>
  <nav class="flex flex-wrap items-center gap-x-2 gap-y-3">
    <template
      v-for="(step, index) in steps"
      :key="step.key"
    >
      <div class="flex items-center gap-2">
        <span
          class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold"
          :class="index < current
            ? 'bg-indigo-600 text-white'
            : index === current
              ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-600'
              : 'bg-gray-100 text-gray-400'"
        >
          {{ index + 1 }}
        </span>
        <span
          class="text-sm"
          :class="index === current ? 'font-medium text-gray-900' : 'text-gray-500'"
        >
          {{ step.label }}
        </span>
      </div>
      <span
        v-if="index < steps.length - 1"
        class="h-px w-6 bg-gray-200"
        aria-hidden="true"
      />
    </template>
  </nav>
</template>

<script setup>
defineProps({
    // [{ key, label }]
    steps: { type: Array, required: true },
    current: { type: Number, default: 0 },
});
</script>
