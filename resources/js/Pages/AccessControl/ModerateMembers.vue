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
    </PageHeader>

    <!-- Applications — on-request groups only (pending applicants awaiting approval). -->
    <RoleApplicationsSection
      v-if="hasApplications"
      :role-id="role.id"
      :applicants="applicants"
    />

    <!-- Members — moderators/admins add and kick. -->
    <RoleMembersSection
      :role-id="role.id"
      :members="members"
    />

    <!-- Moderators — admins only; hidden for automatic (unsupported). -->
    <RoleModeratorsSection
      v-if="role.capabilities.supports_moderators"
      :role-id="role.id"
      :moderators="moderators"
      :can-manage="canManageModerators"
    />
  </div>
</template>

<script setup>
import { computed } from "vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleTypeBadge from "@/Shared/Components/AccessControl/RoleTypeBadge.vue";
import RoleApplicationsSection from "@/Shared/Components/AccessControl/RoleApplicationsSection.vue";
import RoleMembersSection from "@/Shared/Components/AccessControl/RoleMembersSection.vue";
import RoleModeratorsSection from "@/Shared/Components/AccessControl/RoleModeratorsSection.vue";
import { useTranslations } from "@/composables/useTranslations";
import ShowControlGroupsController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ShowControlGroupsController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
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
    canManageModerators: {
        type: Boolean,
        default: false,
    },
});

const { trans } = useTranslations();

// Applications only exist for the request-to-join method.
const hasApplications = computed(() => props.role.capabilities.self_service === "apply");

const breadcrumbs = computed(() => [
    { name: trans("web::access_control.groups"), route: ShowControlGroupsController.url() },
    { name: props.role.name, route: "" },
]);
</script>
