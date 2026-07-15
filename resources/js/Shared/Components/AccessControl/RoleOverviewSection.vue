<template>
  <div class="space-y-6">
    <!-- Identity + join method -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold text-gray-900">
            {{ role.name }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            {{ role.type_description }}
          </p>
        </div>
        <RoleTypeBadge
          :type="role.type"
          :label="role.type_label"
        />
      </div>

      <!-- Your membership + self-service actions -->
      <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
        <span class="text-sm text-gray-500">
          {{ statusLabel }}
        </span>
        <button
          v-if="role.can_join"
          type="button"
          class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
          @click="actions.join(role.id)"
        >
          {{ trans('web::access_control.actions.join') }}
        </button>
        <button
          v-if="role.can_apply"
          type="button"
          class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
          @click="actions.apply(role.id)"
        >
          {{ trans('web::access_control.actions.apply') }}
        </button>
        <button
          v-if="role.can_leave"
          type="button"
          class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50"
          @click="actions.leave(role.id)"
        >
          {{ trans('web::access_control.actions.leave') }}
        </button>
      </div>
    </div>

    <!-- Eligibility (who can join) — only for types that gate on criteria -->
    <EntitySummary
      v-if="role.capabilities?.uses_eligibility"
      :title="trans('web::access_control.eligibility.label')"
      :help="trans('web::access_control.eligibility.help')"
      :entities="role.eligibility?.anyone ? [] : (role.eligibility?.entities ?? [])"
      :empty="role.eligibility?.anyone
        ? trans('web::access_control.eligibility.everyone')
        : trans('web::access_control.eligibility.none')"
    />

    <!-- Applies to (permission scope) — three independent affiliation types -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.applies_to.label') }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ trans('web::access_control.applies_to.help') }}
      </p>

      <p
        v-if="role.applies_to?.everything"
        class="mt-3 text-sm font-medium text-indigo-700"
      >
        {{ trans('web::access_control.applies_to.everything') }}
      </p>
      <template v-else>
        <AppliesToGroup
          :label="trans('web::access_control.applies_to.mode.only_these')"
          :entities="role.applies_to?.allowed ?? []"
          chip-class="bg-indigo-50 text-indigo-700"
        />
        <AppliesToGroup
          :label="trans('web::access_control.applies_to.mode.everyone_except')"
          :entities="role.applies_to?.inverse ?? []"
          chip-class="bg-amber-50 text-amber-700"
        />
        <AppliesToGroup
          :label="trans('web::access_control.applies_to.exclude')"
          :entities="role.applies_to?.excluded ?? []"
          chip-class="bg-rose-50 text-rose-700"
        />
        <p
          v-if="! hasAnyAppliesTo"
          class="mt-3 text-sm text-gray-400"
        >
          {{ trans('web::access_control.applies_to.none') }}
        </p>
      </template>
    </div>

    <!-- Permissions granted -->
    <div
      v-if="role.permissions?.length"
      class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5"
    >
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.permissions') }}
      </h3>
      <div class="mt-3 flex flex-wrap gap-2">
        <span
          v-for="permission in role.permissions"
          :key="permission"
          class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700"
        >
          {{ permission }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import RoleTypeBadge from "./RoleTypeBadge.vue";
import EntitySummary from "./EntitySummary.vue";
import AppliesToGroup from "./AppliesToGroup.vue";
import { useRoleActions } from "@/composables/useRoleActions";
import { useTranslations } from "@/composables/useTranslations";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
});

const actions = useRoleActions();
const { trans } = useTranslations();

const hasAnyAppliesTo = computed(() => Boolean(
    props.role.applies_to?.allowed?.length
    || props.role.applies_to?.inverse?.length
    || props.role.applies_to?.excluded?.length,
));

const statusLabel = computed(() => {
    if (props.role.my_status === "active") {
        return trans("web::access_control.status.active");
    }
    if (props.role.my_status === "pending") {
        return trans("web::access_control.status.pending");
    }
    return trans("web::access_control.status.none");
});
</script>
