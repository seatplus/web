<template>
  <li class="flex items-center justify-between gap-3 py-3">
    <div class="flex min-w-0 items-center gap-3">
      <EveImage
        :object="member.character"
        :size="64"
        tailwind_class="h-8 w-8 rounded-full"
      />
      <span class="truncate text-sm font-medium text-gray-900">
        {{ member.character.name ?? '—' }}
      </span>
      <span
        v-if="badge"
        class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
      >
        {{ badge }}
      </span>
    </div>
    <div class="flex shrink-0 items-center gap-2">
      <slot />
    </div>
  </li>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue";

defineProps({
    // { user_id, status, can_moderate, character: { character_id, name } }
    member: {
        type: Object,
        required: true,
    },
    badge: {
        type: String,
        default: null,
    },
});
</script>
