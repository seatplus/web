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
          <!-- Hub mode: a direct jump into the group's management view for moderators/admins,
               so they don't have to open the group first. Sits at the far right of the card. -->
          <Link
            v-if="hub && canManageMembers"
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

      <!-- Moderator / admin entry points — separate-page mode only. In hub mode these all lead to
           the same page, so the card title alone opens the hub (tabs handle manage/configure). -->
      <Link
        v-if="canManageMembers && ! hub"
        :href="manageUrl"
        class="flex-1 py-3 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        {{ trans('web::access_control.actions.manage_members') }}
      </Link>
      <Link
        v-if="role.can_edit && ! hub"
        :href="configureUrl"
        class="flex-1 py-3 text-center text-sm font-medium text-indigo-600 hover:bg-indigo-50"
      >
        {{ trans('web::access_control.actions.configure') }}
      </Link>
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
import ShowControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ShowControlGroupController";
import ManageMembersController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ManageMembersController";
import RoleHubController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RoleHubController";
import { index as configureController } from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ManageControlGroupMembersController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    // Route the card into the unified hub (single page with tabs) instead of the separate pages.
    hub: {
        type: Boolean,
        default: false,
    },
});

const actions = useRoleActions();
const { trans, trans_choice } = useTranslations();

// Hub mode: the title opens the single hub page (its tabs cover manage/configure). Separate mode:
// the title opens the read-only detail, with per-action links in the footer.
const detailUrl = computed(() => (props.hub
    ? RoleHubController.url({ role_id: props.role.id })
    : ShowControlGroupController.url({ role_id: props.role.id })));
// Footer "manage members" link (separate-page mode only).
const manageUrl = computed(() => ManageMembersController.url({ role_id: props.role.id }));
const configureUrl = computed(() => configureController.url({ role_id: props.role.id }));

// Hub mode: the gear jumps straight into the group's management view — Configure for admins who can
// edit the group, otherwise the Members (moderation) tab. Shown to anyone who can manage or configure.
const gearUrl = computed(() => RoleHubController.url(
    { role_id: props.role.id },
    { query: { tab: props.role.can_edit ? "configure" : "members" } },
));

// Manage-members is reachable by moderators and admins (matches the controller gate).
const canManageMembers = computed(() => props.role.can_moderate || props.role.can_edit);

const hasActions = computed(() =>
    props.role.can_join
    || props.role.can_apply
    || props.role.can_leave
    // The management nav links only exist in separate-page mode.
    || (! props.hub && (props.role.can_edit || canManageMembers.value)));

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
