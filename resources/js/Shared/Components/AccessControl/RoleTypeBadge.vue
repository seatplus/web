<template>
  <span
    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
    :class="classes"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    // The RoleType value: automatic | manual | on-request | opt-in
    type: {
        type: String,
        required: true,
    },
    // Human label (from the role-type metadata / i18n)
    label: {
        type: String,
        required: true,
    },
});

// One colour per join method so the type is scannable at a glance.
const classes = computed(() => ({
    automatic: "bg-slate-100 text-slate-700",
    manual: "bg-blue-100 text-blue-700",
    "on-request": "bg-amber-100 text-amber-800",
    "opt-in": "bg-teal-100 text-teal-800",
}[props.type] ?? "bg-gray-100 text-gray-700"));
</script>
