<template>
  <li class="flex items-center justify-between gap-3 py-3">
    <div class="flex min-w-0 items-center gap-3">
      <EveImage
        v-if="member.character.character_id"
        :object="member.character"
        :size="64"
        tailwind_class="h-8 w-8 shrink-0 rounded-full"
      />
      <span
        v-else
        class="h-8 w-8 shrink-0 rounded-full bg-gray-200"
      />
      <div class="min-w-0">
        <div class="flex items-center gap-2">
          <span class="truncate text-sm font-medium text-gray-900">
            {{ member.character.name ?? '—' }}
          </span>
          <span
            v-if="badge"
            class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
          >
            {{ badge }}
          </span>
        </div>
        <div
          v-if="altNames"
          class="truncate text-xs text-gray-400"
        >
          {{ altNames }}
        </div>
      </div>
    </div>
    <div class="flex shrink-0 items-center gap-2">
      <slot />
    </div>
  </li>
</template>

<script setup>
import { computed } from "vue";
import EveImage from "@/Shared/EveImage.vue";

const props = defineProps({
    // { user_id, status, can_moderate, character: { character_id, name }, characters: [{ character_id, name }] }
    member: {
        type: Object,
        required: true,
    },
    badge: {
        type: String,
        default: null,
    },
});

// The user's other characters (everything except the main), shown as subtext — same as the picker.
const altNames = computed(() => (props.member.characters ?? [])
    .filter((character) => character.character_id !== props.member.character.character_id)
    .map((character) => character.name)
    .filter(Boolean)
    .join(", "));
</script>
