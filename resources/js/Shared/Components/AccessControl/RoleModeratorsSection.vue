<template>
  <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
    <h3 class="text-sm font-semibold text-gray-900">
      {{ trans('web::access_control.sections.moderators') }}
    </h3>

    <!-- Only admins manage moderators. -->
    <div
      v-if="canManage"
      class="mt-3"
    >
      <UserPicker @select="(userId) => actions.addModerator(roleId, userId)" />
    </div>

    <ul
      v-if="moderators.length"
      class="mt-2 divide-y divide-gray-100"
    >
      <MemberRow
        v-for="moderator in moderators"
        :key="moderator.user_id"
        :member="moderator"
      >
        <button
          v-if="canManage"
          type="button"
          class="rounded-md px-3 py-1.5 text-sm font-medium text-rose-600 ring-1 ring-rose-200 hover:bg-rose-50"
          @click="actions.removeModerator(roleId, moderator.user_id)"
        >
          {{ trans('web::access_control.actions.remove_moderator') }}
        </button>
      </MemberRow>
    </ul>
    <p
      v-else
      class="mt-3 text-sm text-gray-400"
    >
      {{ trans('web::access_control.moderate.no_moderators') }}
    </p>
  </section>
</template>

<script setup>
import MemberRow from "./MemberRow.vue";
import UserPicker from "./UserPicker.vue";
import { useRoleActions } from "@/composables/useRoleActions";
import { useTranslations } from "@/composables/useTranslations";

defineProps({
    roleId: {
        type: Number,
        required: true,
    },
    moderators: {
        type: Array,
        default: () => [],
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const actions = useRoleActions();
const { trans } = useTranslations();
</script>
