<template>
  <div class="space-y-6">
    <PageHeader
      :page-title="role.name"
      :breadcrumbs="breadcrumbs"
    >
      <template #secondary>
        <RoleTypeBadge
          :type="role.type"
          :label="role.type_label"
        />
      </template>
      <template
        v-if="canConfigure"
        #primary
      >
        <button
          type="button"
          class="rounded-md px-3 py-2 text-sm font-medium text-rose-600 ring-1 ring-rose-200 hover:bg-rose-50"
          @click="confirmDelete = true"
        >
          {{ trans('web::access_control.actions.delete') }}
        </button>
      </template>
    </PageHeader>

    <!-- Tabs — the discover/moderate/configure surfaces on one page, gated by permission. -->
    <div class="border-b border-gray-200">
      <nav
        class="-mb-px flex gap-6"
        aria-label="Tabs"
      >
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          :class="[
            tab.key === activeTab
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
            'whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium',
          ]"
          @click="activeTab = tab.key"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Overview — read-only summary + self-service join/apply/leave. -->
    <RoleOverviewSection
      v-if="activeTab === 'overview'"
      :role="role"
    />

    <!-- Members — moderators/admins approve, add and kick. -->
    <div
      v-else-if="activeTab === 'members'"
      class="space-y-6"
    >
      <RoleApplicationsSection
        v-if="hasApplications"
        :role-id="role.id"
        :applicants="applicants"
      />
      <RoleMembersSection
        :role-id="role.id"
        :members="members"
      />
      <RoleModeratorsSection
        v-if="role.capabilities.supports_moderators"
        :role-id="role.id"
        :moderators="moderators"
        :can-manage="canManageModerators"
      />
    </div>

    <!-- Configure — admins edit the group (delete lives in the header). -->
    <RoleConfigSection
      v-else-if="activeTab === 'configure'"
      :role="role"
      :join-methods="joinMethods"
      :available-permissions="availablePermissions"
    />

    <teleport to="#destination">
      <ModalWithFooter v-model="confirmDelete">
        <template #title>
          {{ trans('web::access_control.actions.delete') }}
        </template>
        <template #description>
          {{ trans('web::access_control.delete_confirm', { name: role.name }) }}
        </template>
        <template #buttons>
          <button
            type="button"
            class="inline-flex w-full justify-center rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 sm:w-auto"
            @click="destroy"
          >
            {{ trans('web::access_control.actions.delete') }}
          </button>
        </template>
      </ModalWithFooter>
    </teleport>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleTypeBadge from "@/Shared/Components/AccessControl/RoleTypeBadge.vue";
import RoleOverviewSection from "@/Shared/Components/AccessControl/RoleOverviewSection.vue";
import RoleApplicationsSection from "@/Shared/Components/AccessControl/RoleApplicationsSection.vue";
import RoleMembersSection from "@/Shared/Components/AccessControl/RoleMembersSection.vue";
import RoleModeratorsSection from "@/Shared/Components/AccessControl/RoleModeratorsSection.vue";
import RoleConfigSection from "@/Shared/Components/AccessControl/RoleConfigSection.vue";
import ModalWithFooter from "@/Shared/Modals/ModalWithFooter.vue";
import { useTranslations } from "@/composables/useTranslations";
import RoleHubIndexController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RoleHubIndexController";
import DeleteControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/DeleteControlGroupController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    initialTab: {
        type: String,
        default: "overview",
    },
    members: {
        type: Array,
        default: () => [],
    },
    applicants: {
        type: Array,
        default: () => [],
    },
    moderators: {
        type: Array,
        default: () => [],
    },
    joinMethods: {
        type: Array,
        default: () => [],
    },
    availablePermissions: {
        type: Array,
        default: () => [],
    },
    canManageMembers: {
        type: Boolean,
        default: false,
    },
    canConfigure: {
        type: Boolean,
        default: false,
    },
    canManageModerators: {
        type: Boolean,
        default: false,
    },
});

const { trans } = useTranslations();

// Applications only exist for the request-to-join method.
const hasApplications = computed(() => props.role.capabilities.self_service === "apply");

const tabs = computed(() => [
    { key: "overview", label: trans("web::access_control.hub.tabs.overview") },
    ...(props.canManageMembers ? [{ key: "members", label: trans("web::access_control.hub.tabs.members") }] : []),
    ...(props.canConfigure ? [{ key: "configure", label: trans("web::access_control.hub.tabs.configure") }] : []),
]);

// Honour the requested tab only when the viewer is allowed to see it, else fall back to overview.
const allowedInitialTab = tabs.value.some((tab) => tab.key === props.initialTab) ? props.initialTab : "overview";
const activeTab = ref(allowedInitialTab);

const confirmDelete = ref(false);

const breadcrumbs = computed(() => [
    { name: trans("web::access_control.hub.title"), route: RoleHubIndexController.url() },
    { name: props.role.name, route: "" },
]);

const destroy = () => {
    confirmDelete.value = false;
    router.delete(DeleteControlGroupController.url({ role_id: props.role.id }));
};
</script>
