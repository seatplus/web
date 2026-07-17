<template>
  <div class="flex flex-col justify-between rounded-lg bg-white shadow-sm ring-1 ring-black/5">
    <div class="flex-1 p-6">
      <div class="flex items-start justify-between gap-2">
        <Link
          :href="detailUrl"
          class="text-sm font-semibold text-gray-900 hover:text-indigo-600"
        >
          {{ role.name }}
        </Link>
        <div class="flex shrink-0 items-center gap-2">
          <RoleTypeBadge
            :type="role.type"
            :label="role.type_label"
          />
          <!-- A direct jump into the group's management view for moderators/admins, so they don't
               have to open the group first. Configure for those who can edit, else the Members tab. -->
          <Link
            v-if="canManageMembers"
            :href="gearUrl"
            class="text-gray-400 hover:text-gray-600"
            :title="role.can_edit
              ? trans('web::access_control.actions.configure')
              : trans('web::access_control.actions.manage_members')"
          >
            <Cog6ToothIcon class="h-4 w-4" />
          </Link>
        </div>
      </div>

      <p class="mt-2 text-sm text-gray-500">
        {{ role.type_description }}
      </p>

      <div class="mt-4 flex items-center gap-3 text-xs text-gray-500">
        <span>{{ trans_choice('web::access_control.members_count', role.members, { count: role.members }) }}</span>
        <span
          v-if="statusLabel"
          class="inline-flex items-center rounded-full px-2 py-0.5 font-medium"
          :class="role.my_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-teal-100 text-teal-800'"
        >
          {{ statusLabel }}
        </span>
      </div>
    </div>

    <!-- Self-service actions; management lives in the hub tabs (reachable via the title/gear). -->
    <div
      v-if="hasActions"
      class="flex divide-x divide-gray-200 border-t border-gray-200"
    >
      <button
        v-if="role.can_join"
        type="button"
        class="flex-1 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
        @click="actions.join(role.id)"
      >
        {{ trans('web::access_control.actions.join') }}
      </button>
      <button
        v-if="role.can_apply"
        type="button"
        class="flex-1 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
        @click="actions.apply(role.id)"
      >
        {{ trans('web::access_control.actions.apply') }}
      </button>
      <button
        v-if="role.can_leave"
        type="button"
        class="flex-1 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50"
        @click="actions.leave(role.id)"
      >
        {{ trans('web::access_control.actions.leave') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { Cog6ToothIcon } from "@heroicons/vue/24/outline";
import RoleTypeBadge from "./RoleTypeBadge.vue";
import { useRoleActions } from "@/composables/useRoleActions";
import { useTranslations } from "@/composables/useTranslations";
import RoleHubController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RoleHubController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
});

const actions = useRoleActions();
const { trans, trans_choice } = useTranslations();

// The title opens the group's hub (Overview); its tabs cover manage/configure.
const detailUrl = computed(() => RoleHubController.url({ role_id: props.role.id }));

// The gear jumps straight into the management view — Configure for admins who can edit the group,
// otherwise the Members (moderation) tab. Shown to anyone who can manage or configure.
const gearUrl = computed(() => RoleHubController.url(
    { role_id: props.role.id },
    { query: { tab: props.role.can_edit ? "configure" : "members" } },
));

// Manage is reachable by moderators and admins (matches the hub's controller gate).
const canManageMembers = computed(() => props.role.can_moderate || props.role.can_edit);

const hasActions = computed(() =>
    props.role.can_join
    || props.role.can_apply
    || props.role.can_leave);

const statusLabel = computed(() => {
    if (props.role.my_status === "active") {
        return trans("web::access_control.status.active");
    }
    if (props.role.my_status === "pending") {
        return trans("web::access_control.status.pending");
    }
    return null;
});
</script>
